<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\Assertions\RunType;
use App\Assertions\PublicationState;

final class DatasetViewSql implements DatasetViewInterface
{
    private \PDO $pdo;

    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT
            r.type,
            a.pmid,
            d.stable_id, d.version,
            m.psimi_id,
            d.protein1_id, p1.accession AS accession1, d.name1, d.start1, d.stop1, d.mapping1,
            d.protein2_id, p2.accession AS accession2, d.name2, d.start2, d.stop2, d.mapping2
        FROM
            runs AS r,
            associations AS a,
            descriptions AS d,
            methods AS m,
            proteins AS p1,
            proteins AS p2
        WHERE r.type = ?
            AND r.id = a.run_id
            AND a.id = d.association_id
            AND m.id = d.method_id
            AND p1.id = d.protein1_id
            AND p2.id = d.protein2_id
            AND a.state = ?
            AND p1.obsolete IS FALSE
            AND p2.obsolete IS FALSE
            AND d.deleted_at IS NULL
        ORDER BY d.created_at DESC, d.id DESC
    SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(string $type): Statement
    {
        RunType::argument($type);

        $select_descriptions_sth = $this->pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute([$type, PublicationState::CURATED]);

        return Statement::from($this->generator($select_descriptions_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            yield [
                'type' => $row['type'],
                'stable_id' => $row['stable_id'],
                'version' => $row['version'],
                'publication' => [
                    'pmid' => $row['pmid'],
                ],
                'method' => [
                    'psimi_id' => $row['psimi_id'],
                ],
                'interactor1' => [
                    'protein' => [
                        'accession' => $row['accession1'],
                    ],
                    'name' => $row['name1'],
                    'start' => $row['start1'],
                    'stop' => $row['stop1'],
                    'mapping' => json_decode($row['mapping1'], true),
                ],
                'interactor2' => [
                    'protein' => [
                        'accession' => $row['accession2'],
                    ],
                    'name' => $row['name2'],
                    'start' => $row['start2'],
                    'stop' => $row['stop2'],
                    'mapping' => json_decode($row['mapping2'], true),
                ],
            ];
        }
    }
}
