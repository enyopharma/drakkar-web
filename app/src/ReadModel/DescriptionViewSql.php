<?php

declare(strict_types=1);

namespace App\ReadModel;

final class DescriptionViewSql implements DescriptionViewInterface
{
    private \PDO $pdo;

    const COUNT_DESCRIPTIONS_SQL = <<<SQL
        SELECT COUNT(*)
        FROM associations AS a, descriptions AS d
        WHERE a.id = d.association_id
        AND a.run_id = ?
        AND a.pmid = ?
        AND d.stable_id LIKE ?
    SQL;

    const SELECT_DESCRIPTION_SQL = <<<SQL
        SELECT a.run_id, a.pmid
        FROM associations AS a, descriptions AS d
        WHERE a.id = d.association_id AND d.stable_id = ?
        LIMIT 1
    SQL;

    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT
            a.run_id, a.pmid,
            d.id, d.stable_id, d.version, d.created_at, d.deleted_at,
            d.method_id, m.psimi_id,
            d.protein1_id, p1.accession AS accession1, d.name1, d.start1, d.stop1, d.mapping1,
            d.protein2_id, p2.accession AS accession2, d.name2, d.start2, d.stop2, d.mapping2,
            pv1.current_version AS version1,
            pv2.current_version AS version2
        FROM
            associations AS a,
            descriptions AS d,
            methods AS m,
            proteins AS p1
                LEFT JOIN proteins_versions AS pv1
                ON p1.accession = pv1.accession AND p1.version = pv1.version,
            proteins AS p2
                LEFT JOIN proteins_versions AS pv2
                ON p2.accession = pv2.accession AND p2.version = pv2.version
        WHERE a.id = d.association_id
          AND m.id = d.method_id
          AND p1.id = d.protein1_id
          AND p2.id = d.protein2_id
          AND a.run_id = ?
          AND a.pmid = ?
          AND d.stable_id LIKE ?
        ORDER BY
            d.created_at DESC, d.id DESC
        LIMIT ? OFFSET ?
    SQL;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function search(string $stable_id): Statement
    {
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);

        $select_description_sth->execute([$stable_id]);

        $descriptions = $select_description_sth->fetchAll();

        if ($descriptions === false) {
            throw new \LogicException;
        }

        return Statement::from($descriptions);
    }

    public function count(int $run_id, int $pmid, string $stable_id): int
    {
        if ($stable_id == '') $stable_id = '%';

        $count_descriptions_sth = $this->pdo->prepare(self::COUNT_DESCRIPTIONS_SQL);

        $count_descriptions_sth->execute([$run_id, $pmid, $stable_id]);

        return $count_descriptions_sth->fetch(\PDO::FETCH_COLUMN) ?? 0;
    }

    public function all(int $run_id, int $pmid, string $stable_id, int $limit, int $offset): Statement
    {
        if ($stable_id == '') $stable_id = '%';

        $select_descriptions_sth = $this->pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute([$run_id, $pmid, $stable_id, $limit, $offset]);

        return Statement::from($this->generator($select_descriptions_sth));
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($row = $sth->fetch()) {
            yield [
                'id' => $row['id'],
                'pmid' => $row['pmid'],
                'run_id' => $row['run_id'],
                'stable_id' => $row['stable_id'],
                'version' => $row['version'],
                'obsolete' => is_null($row['version1']) || is_null($row['version2']),
                'method' => [
                    'id' => $row['id'],
                    'psimi_id' => $row['psimi_id'],
                ],
                'interactor1' => [
                    'protein' => [
                        'id' => $row['protein1_id'],
                        'accession' => $row['accession1']
                    ],
                    'name' => $row['name1'],
                    'start' => $row['start1'],
                    'stop' => $row['stop1'],
                    'mapping' => json_decode($row['mapping1'], true),
                ],
                'interactor2' => [
                    'protein' => [
                        'id' => $row['protein2_id'],
                        'accession' => $row['accession2']
                    ],
                    'name' => $row['name2'],
                    'start' => $row['start2'],
                    'stop' => $row['stop2'],
                    'mapping' => json_decode($row['mapping2'], true),
                ],
                'created_at' => $this->date($row['created_at']),
                'deleted_at' => $this->date($row['deleted_at']),
                'deleted' => !is_null($row['deleted_at']),
            ];
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
