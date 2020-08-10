<?php

declare(strict_types=1);

namespace App\Actions;

use App\Input\DescriptionInput;

final class StoreDescriptionSql implements StoreDescriptionInterface
{
    const SELECT_METHOD_SQL = <<<SQL
        SELECT * FROM methods WHERE psimi_id = ?
    SQL;

    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT * FROM proteins WHERE accession = ? AND obsolete IS false
    SQL;

    const SELECT_DESCRIPTION_SQL = <<<SQL
        SELECT id
        FROM descriptions
        WHERE association_id = ? AND method_id = ?
        AND protein1_id = ? AND start1 = ? AND stop1 = ?
        AND protein2_id = ? AND start2 = ? AND stop2 = ?
        AND deleted_at IS NULL
    SQL;

    const INSERT_DESCRIPTION_SQL = <<<SQL
        INSERT INTO descriptions
        (
            stable_id, version,
            association_id, method_id,
            protein1_id, name1, start1, stop1, mapping1,
            protein2_id, name2, start2, stop2, mapping2
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    SQL;

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(DescriptionInput $input): StoreDescriptionResult
    {
        // exctract data from the input.
        $data = $input->data();

        $association_id = $data['association_id'];
        $method_id = $data['method_id'];
        $interactor1 = $data['interactor1'];
        $interactor2 = $data['interactor2'];

        // prepare the queries.
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);
        $insert_description_sth = $this->pdo->prepare(self::INSERT_DESCRIPTION_SQL);

        // ensure description does not exists (both directions).
        $select_description_sth->execute([
            $association_id,
            $method_id,
            $interactor1['protein_id'],
            $interactor1['start'],
            $interactor1['stop'],
            $interactor2['protein_id'],
            $interactor2['start'],
            $interactor2['stop'],
        ]);

        if ($select_description_sth->fetch()) {
            return StoreDescriptionResult::descriptionAlreadyExists();
        }

        $select_description_sth->execute([
            $association_id,
            $method_id,
            $interactor2['protein_id'],
            $interactor2['start'],
            $interactor2['stop'],
            $interactor1['protein_id'],
            $interactor1['start'],
            $interactor1['stop'],
        ]);

        if ($select_description_sth->fetch()) {
            return StoreDescriptionResult::descriptionAlreadyExists();
        }

        // insert the description.
        $this->pdo->beginTransaction();

        $tries = 0;
        $inserted = false;

        while (!$inserted && $tries < 10) {
            try {
                $tries++;

                $inserted = $insert_description_sth->execute([
                    'EY' . strtoupper(bin2hex(random_bytes(4))),
                    1,
                    $association_id,
                    $method_id,
                    $interactor1['protein_id'],
                    $interactor1['name'],
                    $interactor1['start'],
                    $interactor1['stop'],
                    json_encode($interactor1['mapping']),
                    $interactor2['protein_id'],
                    $interactor2['name'],
                    $interactor2['start'],
                    $interactor2['stop'],
                    json_encode($interactor2['mapping']),
                ]);
            }

            catch (\PDOException $e) {
                die(var_dump($e->getMessage()));
                $inserted = false;
            }
        }

        if (!$inserted) {
            $this->pdo->rollback();

            return StoreDescriptionResult::stableIdFailure();
        }

        $this->pdo->commit();

        $description['id'] = (int) $this->pdo->lastInsertId();

        return StoreDescriptionResult::success($description);
    }
}
