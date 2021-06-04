<?php

declare(strict_types=1);

namespace App\ReadModel;

final class TaxonViewSql implements TaxonViewInterface
{
    const SELECT_TAXON_SQL = <<<SQL
        SELECT left_value, right_value FROM taxon WHERE ncbi_taxon_id = ?
    SQL;

    const SELECT_NAMES_SQL = <<<SQL
        SELECT d.name2 AS name
        FROM proteins AS p, descriptions AS d
        WHERE p.id = d.protein2_id
        AND p.ncbi_taxon_id = ?
        AND d.deleted_at IS NULL
        GROUP BY d.name2
        ORDER BY d.name2 ASC
    SQL;

    public function __construct(
        private \PDO $pdo,
    ) {
    }

    public function id(int $ncbi_taxon_id, string ...$with): Statement
    {
        $select_taxon_sth = $this->pdo->prepare(self::SELECT_TAXON_SQL);

        $select_taxon_sth->execute([$ncbi_taxon_id]);

        if (!$taxon = $select_taxon_sth->fetch()) {
            return Statement::from([]);
        }

        if (in_array('names', $with)) {
            $taxon['names'] = $this->names($ncbi_taxon_id);
        }

        return Statement::from([$taxon]);
    }

    private function names(int $ncbi_taxon_id): array
    {
        $select_names_sth = $this->pdo->prepare(self::SELECT_NAMES_SQL);

        $select_names_sth->execute([$ncbi_taxon_id]);

        $names = [];

        while (['name' => $name] = $select_names_sth->fetch()) {
            $names[$name] = 1;
        }

        return array_keys($names);
    }
}
