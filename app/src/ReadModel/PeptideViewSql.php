<?php

declare(strict_types=1);

namespace App\ReadModel;

final class PeptideViewSql implements PeptideViewInterface
{
    const SELECT_DATA_SQL = <<<SQL
        SELECT p.type, p.sequence, p.data
        FROM runs AS r, associations AS a, descriptions as d, peptides AS p
        WHERE r.id = a.run_id AND a.id = d.association_id AND d.stable_id = p.stable_id
        AND r.id = ? AND a.pmid = ? AND d.id = ?
    SQL;

    const SELECT_PEPTIDES_SQL = <<<SQL
        SELECT d.id, d.stable_id, r.type, m.sequence
        FROM runs AS r, associations AS a, descriptions as d,
            json_to_recordset(%s) AS m (sequence text)
        WHERE r.id = a.run_id AND a.id = d.association_id
        AND r.id = ? AND a.pmid = ? AND d.id = ?
        AND LENGTH(sequence) >= ?
        AND LENGTH(sequence) <= ?
        GROUP BY d.id, d.stable_id, r.type, m.sequence
    SQL;

    public function __construct(private \PDO $pdo)
    {
    }

    public function all(int $run_id, int $pmid, int $id): Statement
    {
        // select the peptides data associated with this description.
        $select_data_sth = $this->pdo->prepare(self::SELECT_DATA_SQL);

        $select_data_sth->execute([$run_id, $pmid, $id]);

        // select the peptides associated with this description.
        $params = [$run_id, $pmid, $id, self::MIN_LENGTH, self::MAX_LENGTH];

        $select_peptides1_sth = $this->pdo->prepare(sprintf(self::SELECT_PEPTIDES_SQL, 'mapping1'));
        $select_peptides2_sth = $this->pdo->prepare(sprintf(self::SELECT_PEPTIDES_SQL, 'mapping2'));

        $select_peptides1_sth->execute($params);
        $select_peptides2_sth->execute($params);

        return Statement::from($this->generator(
            $select_data_sth,
            $select_peptides1_sth,
            $select_peptides2_sth,
        ));
    }

    private function generator(\PDOStatement $select_data_sth, \PDOStatement $rows1, \PDOStatement $rows2): \Generator
    {
        $map = [];

        while ([$type, $sequence, $data] = $select_data_sth->fetch(\PDO::FETCH_NUM)) {
            $map[$type][$sequence] = $data;
        }

        while ([$id, $type, $stable_id, $sequence] = $rows1->fetch(\PDO::FETCH_NUM)) {
            yield [
                'description_id' => $id,
                'stable_id' => $stable_id,
                'type' => 'h',
                'sequence' => $sequence,
                'data' => json_decode($map['h'][$sequence] ?? '{}', true),
            ];
        }

        while ([$id, $type, $stable_id, $sequence] = $rows2->fetch(\PDO::FETCH_NUM)) {
            $ptype = $type == 'hh' ? 'h' : 'v';

            yield [
                'description_id' => $id,
                'stable_id' => $stable_id,
                'type' => $ptype,
                'sequence' => $sequence,
                'data' => json_decode($map[$ptype][$sequence] ?? '{}', true),
            ];
        }
    }
}
