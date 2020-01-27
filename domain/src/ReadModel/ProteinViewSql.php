<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class ProteinViewSql implements ProteinViewInterface
{
    private $pdo;

    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT
            p.id, p.type, p.accession, tn.name AS taxon, p.name, p.description, s.sequence
        FROM
            proteins AS p, sequences AS s, taxon AS t, taxon_name AS tn
        WHERE
            p.id = s.protein_id AND
            s.is_canonical IS TRUE AND
            p.taxon_id = t.ncbi_taxon_id AND
            t.taxon_id = tn.taxon_id AND
            tn.name_class = 'scientific name' AND
            p.accession = ?
SQL;

    const SELECT_PROTEINS_SQL = <<<SQL
        SELECT
            p.id, p.type, p.accession, tn.name AS taxon, p.name, p.description
        FROM
            proteins AS p, taxon AS t, taxon_name AS tn
        WHERE
            %s AND
            p.type = ? AND
            p.taxon_id = t.ncbi_taxon_id AND
            t.taxon_id = tn.taxon_id AND
            tn.name_class = 'scientific name'
        LIMIT ?
SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function accession(string $accession): Statement
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);

        $select_protein_sth->execute([$accession]);

        return Statement::from($this->generator($select_protein_sth));
    }

    public function search(string $type, string $query, int $limit): Statement
    {
        $qs = array_map(function ($q) {
            return '%' . trim($q) . '%';
        }, array_filter(explode('+', $query)));

        if (count($qs) == 0) {
            return Statement::from([]);
        }

        $where = implode(' AND ', array_pad([], count($qs), 'search ILIKE ?'));

        $select_proteins_sth = $this->pdo->prepare(sprintf(self::SELECT_PROTEINS_SQL, $where));

        $select_proteins_sth->execute([...$qs, $type, $limit]);

        return Statement::from($this->generator($select_proteins_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            yield new ProteinSql(
                $this->pdo,
                $row['id'],
                $row['type'],
                $row['accession'],
                $row
            );
        }
    }
}
