<?php

declare(strict_types=1);

namespace App\ReadModel;

final class DescriptionViewSql implements DescriptionViewInterface
{
    private \PDO $pdo;

    private int $run_id;

    private int $pmid;

    private array $data;

    const COUNT_DESCRIPTIONS_SQL = <<<SQL
        SELECT COUNT(*)
        FROM associations AS a, descriptions AS d
        WHERE a.id = d.association_id
        AND a.run_id = ?
        AND a.pmid = ?
SQL;

    const SELECT_DESCRIPTION_SQL = <<<SQL
        SELECT d.id, d.stable_id, d.created_at, d.deleted_at,
            m.psimi_id,
            i1.name AS name1, i1.start AS start1, i1.stop AS stop1, i1.mapping AS mapping1,
            i2.name AS name2, i2.start AS start2, i2.stop AS stop2, i2.mapping AS mapping2,
            p1.accession AS accession1,
            p2.accession AS accession2
        FROM associations AS a,
            descriptions AS d,
            methods AS m,
            interactors AS i1, interactors AS i2,
            proteins AS p1, proteins AS p2
        WHERE a.id = d.association_id
        AND m.id = d.method_id
        AND i1.id = d.interactor1_id
        AND i2.id = d.interactor2_id
        AND p1.id = i1.protein_id
        AND p2.id = i2.protein_id
        AND a.run_id = ?
        AND a.pmid = ?
        AND d.id = ?
SQL;

    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT
            d.id, d.stable_id, d.created_at, d.deleted_at,
            m.psimi_id,
            i1.name AS name1, i1.start AS start1, i1.stop AS stop1, i1.mapping AS mapping1,
            i2.name AS name2, i2.start AS start2, i2.stop AS stop2, i2.mapping AS mapping2,
            p1.accession AS accession1,
            p2.accession AS accession2
        FROM
            associations AS a,
            descriptions AS d,
            methods AS m,
            interactors AS i1, interactors AS i2,
            proteins AS p1, proteins AS p2
        WHERE
            a.id = d.association_id AND
            m.id = d.method_id AND
            i1.id = d.interactor1_id AND
            i2.id = d.interactor2_id AND
            p1.id = i1.protein_id AND
            p2.id = i2.protein_id AND
            a.run_id = ? AND
            a.pmid = ?
        ORDER BY
            d.created_at DESC, d.id DESC
        LIMIT ? OFFSET ?
SQL;

    public function __construct(\PDO $pdo, int $run_id, int $pmid, array $data = [])
    {
        $this->pdo = $pdo;
        $this->run_id = $run_id;
        $this->pmid = $pmid;
        $this->data = $data;
    }

    public function count(): int
    {
        $count_descriptions_sth = $this->pdo->prepare(self::COUNT_DESCRIPTIONS_SQL);

        $count_descriptions_sth->execute([$this->run_id, $this->pmid]);

        return $count_descriptions_sth->fetch(\PDO::FETCH_COLUMN) ?? 0;
    }

    public function id(int $id): Statement
    {
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);

        $select_description_sth->execute([$this->run_id, $this->pmid, $id]);

        return Statement::from($this->generator($select_description_sth));
    }

    public function all(int $limit, int $offset): Statement
    {
        $select_descriptions_sth = $this->pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute([$this->run_id, $this->pmid, $limit, $offset]);

        return Statement::from($this->generator($select_descriptions_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            $description = [
                'stable_id' => $row['stable_id'],
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
                    'mapping' => is_null($row['mapping1']) ? [] : json_decode($row['mapping1'], true),
                ],
                'interactor2' => [
                    'protein' => [
                        'accession' => $row['accession2'],
                    ],
                    'name' => $row['name2'],
                    'start' => $row['start2'],
                    'stop' => $row['stop2'],
                    'mapping' => is_null($row['mapping2']) ? [] : json_decode($row['mapping2'], true),
                ],
                'created_at' => $this->date($row['created_at']),
                'deleted_at' => $this->date($row['deleted_at']),
                'deleted' => ! is_null($row['deleted_at']),
            ];

            yield new DescriptionSql(
                $this->pdo,
                $this->run_id,
                $this->pmid,
                $row['id'],
                $description + $this->data
            );
        }
    }

    private function date(string $date = null): string
    {
        if (is_null($date)) return '-';

        if (($time = strtotime($date)) !== false) {
            return date('Y - m - d', $time);
        }

        throw new \LogicException(
            sprintf('%s can\'t be converted to a time', $date)
        );
    }
}
