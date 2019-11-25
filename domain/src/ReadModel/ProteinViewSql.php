<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class ProteinViewSql implements ProteinViewInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function accession(string $accession): Statement
    {
        $select_protein_sth = Query::instance($this->pdo)
            ->select('p.id, p.type, p.accession, tn.name AS taxon, p.name, p.description, s.sequence')
            ->from('proteins AS p, sequences AS s, taxon AS t, taxon_name AS tn')
            ->where('p.id = s.protein_id AND s.is_canonical IS TRUE')
            ->where('p.taxon_id = t.ncbi_taxon_id')
            ->where('t.taxon_id = tn.taxon_id')
            ->where('tn.name_class = \'scientific name\'')
            ->where('p.accession = ?')
            ->prepare();

        $select_protein_sth->execute([$accession]);

        return new Statement(
            $this->first($select_protein_sth)
        );
    }

    public function search(string $type, string $query, int $limit): Statement
    {
        $qs = array_map(function ($q) {
            return '%' . trim($q) . '%';
        }, array_filter(explode('+', $query)));

        $select_proteins_sth = Query::instance($this->pdo)
            ->select('p.type, p.accession, tn.name AS taxon, p.name, p.description')
            ->from('proteins AS p, taxon AS t, taxon_name AS tn')
            ->where('p.type = ?', ...array_pad([], count($qs), 'p.search ILIKE ?'))
            ->where('p.taxon_id = t.ncbi_taxon_id')
            ->where('t.taxon_id = tn.taxon_id')
            ->where('tn.name_class = \'scientific name\'')
            ->sliced()
            ->prepare();

        $select_proteins_sth->execute(array_merge([$type], $qs, [$limit, 0]));

        return new Statement(
            $this->generator($select_proteins_sth)
        );
    }

    private function first(\PDOStatement $sth): \Generator
    {
        if ($protein = $sth->fetch()) {
            $protein_id = $protein['id'];

            unset($protein['id']);

            yield array_merge($protein, [
                'isoforms' => $this->isoforms($protein_id),
                'chains' => $this->chains($protein_id),
                'domains' => $this->domains($protein_id),
                'matures' => $this->matures($protein_id),
            ]);
        }
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        yield from (array) $sth->fetchAll();
    }

    private function isoforms(int $protein_id): array
    {
        $select_isoforms_sth = Query::instance($this->pdo)
            ->select('accession, sequence, is_canonical')
            ->from('sequences')
            ->where('protein_id = ?')
            ->orderby('is_canonical DESC, accession ASC')
            ->prepare();

        $select_isoforms_sth->execute([$protein_id]);

        return (array) $select_isoforms_sth->fetchAll();
    }

    private function chains(int $protein_id): array
    {
        $select_chains_sth = Query::instance($this->pdo)
            ->select('f.key, f.description, f.start, f.stop')
            ->from('sequences AS s, features AS f')
            ->where('s.id = f.sequence_id')
            ->where('s.is_canonical IS TRUE')
            ->where('s.protein_id = ?')
            ->where('f.key = \'CHAIN\'')
            ->orderby('f.start ASC, f.stop ASC')
            ->prepare();

        $select_chains_sth->execute([$protein_id]);

        return (array) $select_chains_sth->fetchAll();
    }

    private function domains(int $protein_id): array
    {
        $select_domains_sth = Query::instance($this->pdo)
            ->select('f.key, f.description, f.start, f.stop')
            ->from('sequences AS s, features AS f')
            ->where('s.id = f.sequence_id')
            ->where('s.is_canonical IS TRUE')
            ->where('s.protein_id = ?')
            ->where('f.key IN (\'TOPO_DOM\', \'TRANSMEM\', \'INTRAMEM\', \'DOMAIN\', \'REGION\', \'MOTIF\')')
            ->orderby('f.start ASC, f.stop ASC')
            ->prepare();

        $select_domains_sth->execute([$protein_id]);

        return (array) $select_domains_sth->fetchAll();
    }

    private function matures(int $protein_id): array
    {
        $select_matures_sth = Query::instance($this->pdo)
            ->select('i.name, i.start, i.stop')
            ->from('descriptions AS d, interactors AS i')
            ->where('(i.id = d.interactor1_id OR i.id = d.interactor2_id)')
            ->where('d.deleted_at IS NULL')
            ->where('i.protein_id = ?')
            ->groupby('i.name, i.start, i.stop')
            ->prepare();

        $select_matures_sth->execute([$protein_id]);

        return (array) $select_matures_sth->fetchAll();
    }
}
