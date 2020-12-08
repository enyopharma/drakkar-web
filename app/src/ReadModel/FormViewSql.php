<?php

declare(strict_types=1);

namespace App\ReadModel;

final class FormViewSql implements FormViewInterface
{
    const SELECT_DESCRIPTION_SQL = <<<SQL
        SELECT
            a.run_id, a.pmid,
            d.id, d.stable_id, d.method_id,
            d.protein1_id, d.name1, d.start1, d.stop1, d.mapping1,
            d.protein2_id, d.name2, d.start2, d.stop2, d.mapping2
        FROM associations AS a, descriptions AS d
        WHERE a.id = d.association_id
            AND a.run_id = ?
            AND a.pmid = ?
            AND d.id = ?
    SQL;

    public function __construct(
        private \PDO $pdo,
    ) {}

    public function id(int $run_id, int $pmid, int $id): Statement
    {
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);

        if ($select_description_sth === false) throw new \Exception;

        $select_description_sth->execute([$run_id, $pmid, $id]);

        return Statement::from($this->generator($select_description_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            yield [
                'run_id' => $row['run_id'],
                'pmid' => $row['pmid'],
                'id' => $row['id'],
                'stable_id' => $row['stable_id'],
                'method_id' => $row['method_id'],
                'interactor1' => [
                    'protein_id' => $row['protein1_id'],
                    'name' => $row['name1'],
                    'start' => $row['start1'],
                    'stop' => $row['stop1'],
                    'mapping' => json_decode($row['mapping1'], true),
                ],
                'interactor2' => [
                    'protein_id' => $row['protein2_id'],
                    'name' => $row['name2'],
                    'start' => $row['start2'],
                    'stop' => $row['stop2'],
                    'mapping' => json_decode($row['mapping2'], true),
                ],
            ];
        }
    }
}
