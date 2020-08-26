<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\Assertions\ProteinType;

final class ProteinViewSql implements ProteinViewInterface
{
    private \PDO $pdo;

    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT p.id, p.type, p.accession, p.version, p.name, p.description,
            p.sequences->>p.accession AS sequence, p.sequences,
            COALESCE(tn.name, 'obsolete taxon') AS taxon
        FROM
            proteins AS p,
            taxon AS t LEFT JOIN taxon_name AS tn ON t.taxon_id = tn.taxon_id AND tn.name_class = 'scientific name'
        WHERE p.ncbi_taxon_id = t.ncbi_taxon_id
        AND p.id = ?
    SQL;

    const SELECT_PROTEINS_SQL = <<<SQL
        SELECT p.id, p.type, p.accession, p.name, p.description, COALESCE(tn.name, 'obsolete taxon') AS taxon
        FROM
            proteins AS p,
            proteins_versions AS v,
            taxon AS t LEFT JOIN taxon_name AS tn ON t.taxon_id = tn.taxon_id AND tn.name_class = 'scientific name'
        WHERE p.type = ? AND %s
        AND p.accession = v.accession AND p.version = v.version
        AND p.ncbi_taxon_id = t.ncbi_taxon_id
        LIMIT ?
    SQL;

    const SELECT_DOMAINS_SQL = <<<SQL
        SELECT f->>'type' AS type, f->>'start' AS start, f->>'stop' AS stop, f->>'description' AS description
        FROM proteins_versions AS v, jsonb_array_elements(v.features) AS f
        WHERE v.accession = ? AND v.version = ? AND f->>'type' IN (
            'topological domain',
            'transmembrane region',
            'intramembrane region',
            'domain',
            'region of interest',
            'short sequence motif'
        )
    SQL;

    const SELECT_CHAINS_SQL = <<<SQL
        SELECT f->>'type' AS type, f->>'start' AS start, f->>'stop' AS stop, f->>'description' AS description
        FROM proteins_versions AS v, jsonb_array_elements(v.features) AS f
        WHERE v.accession = ? AND v.version = ? AND f->>'type' = 'chain'
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

        ['accession' => $accession, 'version' => $version] = $protein;

        $sequences = json_decode($protein['sequences'], true);

        if (in_array('isoforms', $with)) {
            $protein['isoforms'] = $this->isoforms($accession, $sequences);
        }

        if (in_array('domains', $with)) {
            $protein['domains'] = $this->domains($accession, $version);
        }

        if (in_array('chains', $with)) {
            $protein['chains'] = $this->chains($accession, $version);
        }

        if (in_array('matures', $with)) {
            $protein['matures'] = $protein['type'] == ProteinType::H ? [] : $this->matures($id);
        }

        unset($protein['sequences']);

        return Statement::from([$protein]);
    }

    public function search(string $type, string $query, int $limit): Statement
    {
        ProteinType::argument($type);

        $qs = explode('+', $query);
        $qs = array_map('trim', $qs);
        $qs = array_filter($qs, fn ($q) => strlen($q) > 2);
        $qs = array_map(fn ($q) => '%' . $q . '%', $qs);

        if (count($qs) == 0) {
            return Statement::from([]);
        }

        $where = implode(' AND ', array_pad([], count($qs), 'v.search ILIKE ?'));

        $select_proteins_sth = $this->pdo->prepare(sprintf(self::SELECT_PROTEINS_SQL, $where));

        $select_proteins_sth->execute([$type, ...$qs, $limit]);

        return Statement::from($select_proteins_sth);
    }

    private function isoforms(string $canonical, array $sequences): array
    {
        $map = fn (string $accession, string $sequence) => [
            'accession' => $accession,
            'sequence' => $sequence,
            'is_canonical' => $canonical == $accession,
        ];

        return array_map($map, array_keys($sequences), $sequences);
    }

    private function domains(string $accession, string $version): array
    {
        $select_domains_sth = $this->pdo->prepare(self::SELECT_DOMAINS_SQL);

        $select_domains_sth->execute([$accession, $version]);

        $domains = $select_domains_sth->fetchAll();

        if ($domains === false) {
            throw new \LogicException;
        }

        return $domains;
    }

    private function chains(string $accession, string $version): array
    {
        $select_chains_sth = $this->pdo->prepare(self::SELECT_CHAINS_SQL);

        $select_chains_sth->execute([$accession, $version]);

        $chains = $select_chains_sth->fetchAll();

        if ($chains === false) {
            throw new \LogicException;
        }

        return $chains;
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
