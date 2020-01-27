<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class DatasetViewSql implements DatasetViewInterface
{
    private $pdo;

    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT r.type, d.stable_id, a.pmid, m.psimi_id,
            i1.name AS name1, i1.start AS start1, i1.stop AS stop1, i1.mapping AS mapping1,
            i2.name AS name2, i2.start AS start2, i2.stop AS stop2, i2.mapping AS mapping2,
            p1.id AS protein1_id, p1.accession AS accession1,
            p2.id AS protein2_id, p2.accession AS accession2
        FROM runs AS r, associations AS a, descriptions AS d, methods AS m,
            interactors AS i1, interactors AS i2,
            proteins AS p1, proteins AS p2
        WHERE r.type = ?
        AND r.id = a.run_id
        AND a.id = d.association_id
        AND m.id = d.method_id
        AND i1.id = d.interactor1_id
        AND i2.id = d.interactor2_id
        AND p1.id = i1.protein_id
        AND p2.id = i2.protein_id
        AND a.state = 'curated'
        AND d.deleted_at IS NULL
        AND d.created_at DESC, d.id DESC
SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(string $type): Statement
    {
        $select_descriptions_sth = $this->pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute([$type]);

        return Statement::from($this->generator($select_descriptions_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            yield new Entity([
                'type' => $row['type'],
                'stable_id' => $row['stable_id'],
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
            ]);
        }
    }
}
