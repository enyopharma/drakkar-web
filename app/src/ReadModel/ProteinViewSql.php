<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\Assertions\ProteinType;

final class ProteinViewSql implements ProteinViewInterface
{
    private \PDO $pdo;

    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT p.id, p.type, p.accession, tn.name AS taxon, p.name, p.description, s.sequence
        FROM proteins AS p, sequences AS s, taxon AS t, taxon_name AS tn
        WHERE p.id = s.protein_id
        AND s.is_canonical IS TRUE
        AND p.ncbi_taxon_id = t.ncbi_taxon_id
        AND t.taxon_id = tn.taxon_id
        AND tn.name_class = 'scientific name'
        AND p.id = ?
    SQL;

    const SELECT_PROTEINS_SQL = <<<SQL
        SELECT p.id, p.type, p.accession, tn.name AS taxon, p.name, p.description
        FROM proteins AS p, taxon AS t, taxon_name AS tn
        WHERE %s
        AND p.type = ?
        AND p.obsolete IS FALSE
        AND p.ncbi_taxon_id = t.ncbi_taxon_id
        AND t.taxon_id = tn.taxon_id
        AND tn.name_class = 'scientific name'
        LIMIT ?
    SQL;

    const SELECT_ISOFORMS_SQL = <<<SQL
        SELECT accession, sequence, is_canonical
        FROM sequences
        WHERE protein_id = ?
        ORDER BY is_canonical DESC, accession ASC
    SQL;

    const SELECT_CHAINS_SQL = <<<SQL
        SELECT f.key, f.description, f.start, f.stop
        FROM sequences AS s, features AS f
        WHERE s.id = f.sequence_id
        AND s.is_canonical IS TRUE
        AND s.protein_id = ?
        AND f.key = 'CHAIN'
        ORDER BY f.start ASC, f.stop ASC
    SQL;

    const SELECT_DOMAINS_SQL = <<<SQL
        SELECT f.key, f.description, f.start, f.stop
        FROM sequences AS s, features AS f
        WHERE s.id = f.sequence_id
        AND s.is_canonical IS TRUE
        AND s.protein_id = ?
        AND f.key IN ('TOPO_DOM', 'TRANSMEM', 'INTRAMEM', 'DOMAIN', 'REGION', 'MOTIF')
        ORDER BY f.start ASC, f.stop ASC
    SQL;

    # ASSUME THE VIRAL INTERACTOR IS ALWAYS THE SECOND INTERACTOR
    const SELECT_MATURES_SQL = <<<SQL
        SELECT name2 AS name, start2 AS start, stop2 AS stop
        FROM descriptions
        WHERE protein2_id = ? AND deleted_at IS NULL
        GROUP BY name2, start2, stop2
    SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function id(int $id, string ...$with): Statement
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);

        $select_protein_sth->execute([$id]);

        if (!$protein = $select_protein_sth->fetch()) {
            return Statement::from([]);
        }

        if (in_array('isoforms', $with)) {
            $protein['isoforms'] = $this->isoforms($id);
        }

        if (in_array('chains', $with)) {
            $protein['chains'] = $this->chains($id);
        }

        if (in_array('domains', $with)) {
            $protein['domains'] = $this->domains($id);
        }

        if (in_array('matures', $with)) {
            $protein['matures'] = $this->matures($id);
        }

        return Statement::from([$protein]);
    }

    public function search(string $type, string $query, int $limit): Statement
    {
        ProteinType::argument($type);

        $qs = explode('+', $query);
        $qs = array_filter($qs);
        $qs = array_map(fn ($q) => '%' . trim($q) . '%', $qs);

        if (count($qs) == 0) {
            return Statement::from([]);
        }

        $where = implode(' AND ', array_pad([], count($qs), 'search ILIKE ?'));

        $select_proteins_sth = $this->pdo->prepare(sprintf(self::SELECT_PROTEINS_SQL, $where));

        $select_proteins_sth->execute([...$qs, $type, $limit]);

        return Statement::from($select_proteins_sth);
    }

    private function isoforms(int $id): array
    {
        $select_isoforms_sth = $this->pdo->prepare(self::SELECT_ISOFORMS_SQL);

        $select_isoforms_sth->execute([$id]);

        $isoforms = $select_isoforms_sth->fetchAll();

        if ($isoforms === false) {
            throw new \LogicException;
        }

        return $isoforms;
    }

    private function chains(int $id): array
    {
        $select_chains_sth = $this->pdo->prepare(self::SELECT_CHAINS_SQL);

        $select_chains_sth->execute([$id]);

        $chains = $select_chains_sth->fetchAll();

        if ($chains === false) {
            throw new \LogicException;
        }

        return $chains;
    }

    private function domains(int $id): array
    {
        $select_domains_sth = $this->pdo->prepare(self::SELECT_DOMAINS_SQL);

        $select_domains_sth->execute([$id]);

        $domains = $select_domains_sth->fetchAll();

        if ($domains === false) {
            throw new \LogicException;
        }

        return $domains;
    }

    private function matures(int $id): array
    {
        $select_matures_sth = $this->pdo->prepare(self::SELECT_MATURES_SQL);

        $select_matures_sth->execute([$id]);

        $matures = $select_matures_sth->fetchAll();

        if ($matures === false) {
            throw new \LogicException;
        }

        return $matures;
    }
}
