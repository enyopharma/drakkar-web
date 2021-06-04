<?php

declare(strict_types=1);

namespace App\ReadModel;

final class ProteinNameViewSql implements ProteinNameViewInterface
{
    const SELECT_TAXON_SQL = <<<SQL
        SELECT t.left_value, t.right_value
        FROM taxon AS t, proteins AS p
        WHERE t.ncbi_taxon_id = p.ncbi_taxon_id
        AND p.id = ?
    SQL;

    const SELECT_NAMES_SQL = <<<SQL
        SELECT DISTINCT d.name2 AS name
        FROM
            taxon AS t,
            proteins AS p,
            descriptions AS d
        WHERE t.ncbi_taxon_id = p.ncbi_taxon_id
        AND p.id = d.protein2_id
        AND t.left_value >= ?
        AND t.right_value <= ?
        AND d.deleted_at IS NULL
        ORDER BY d.name2 ASC
    SQL;

    public function __construct(
        private \PDO $pdo,
    ) {
    }

    public function names(int $id): Statement
    {
        $select_taxon_sth = $this->pdo->prepare(self::SELECT_TAXON_SQL);

        $select_taxon_sth->execute([$id]);

        if (!$taxon = $select_taxon_sth->fetch()) {
            return Statement::from([]);
        }

        $select_names_sth = $this->pdo->prepare(self::SELECT_NAMES_SQL);

        $select_names_sth->execute(array_values($taxon));

        $names = [];

        while (['name' => $name] = $select_names_sth->fetch()) {
            $names[$name] = 1;
        }

        return Statement::from(array_keys($names));
    }
}
