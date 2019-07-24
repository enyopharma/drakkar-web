<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

final class ProteinViewSql implements ProteinViewInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function accession(string $accession)
    {
        $select_protein_sth = Query::instance($this->pdo)
            ->select('p.id, p.type, p.accession, p.name, p.description, s.sequence')
            ->from('proteins AS p, sequences AS s')
            ->where('p.id = s.protein_id AND s.is_canonical IS TRUE')
            ->where('p.accession = ?')
            ->prepare();

        $select_protein_sth->execute([$accession]);

        if ($protein = $select_protein_sth->fetch()) {
            return array_merge($protein, [
                'isoforms' => $this->isoforms($protein['id']),
                'chains' => $this->chains($protein['id']),
                'domains' => $this->domains($protein['id']),
                'matures' => $this->matures($protein['id']),
            ]);
        }

        return false;
    }

    public function search(string $type, string $q, int $limit): array
    {
        $qs = array_map(function ($q) { return '%' . $q . '%'; }, array_filter(explode(' ', $q)));

        $select_proteins_sth = Query::instance($this->pdo)
            ->select('type, accession, name, description')
            ->from('proteins')
            ->where('type = ?', ...array_pad([], count($qs), 'search ILIKE ?'))
            ->sliced()
            ->prepare();

        $select_proteins_sth->execute(array_merge([$type], $qs, [$limit, 0]));

        return (array) $select_proteins_sth->fetchAll();
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
