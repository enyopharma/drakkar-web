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
        WHERE association_id = ? AND stable_id != ? AND method_id = ?
        AND protein1_id = ? AND start1 = ? AND stop1 = ?
        AND protein2_id = ? AND start2 = ? AND stop2 = ?
        AND deleted_at IS NULL
    SQL;

    const UPDATE_DESCRIPTIONS_SQL = <<<SQL
        UPDATE descriptions SET deleted_at = NOW()
        WHERE stable_id = ? AND deleted_at IS NULL
    SQL;

    const SELECT_MAX_VERSION_SQL = <<<SQL
        SELECT MAX(version) FROM descriptions WHERE stable_id = ? GROUP BY stable_id
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

        $stable_id = $data['stable_id'];
        $association_id = $data['association_id'];
        $method_id = $data['method_id'];
        $interactor1 = $data['interactor1'];
        $interactor2 = $data['interactor2'];

        // ensure description does not exists.
        if ($this->isExisting($association_id, $stable_id, $method_id, $interactor1, $interactor2)) {
            return StoreDescriptionResult::descriptionAlreadyExists();
        }

        // insert the description.
        return $stable_id == ''
            ? $this->insertFirstVersion($association_id, $method_id, $interactor1, $interactor2)
            : $this->insertNewVersion($association_id, $stable_id, $method_id, $interactor1, $interactor2);
    }

    private function isExisting(int $association_id, string $stable_id, int $method_id, array $interactor1, array $interactor2): bool
    {
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);

        $select_description_sth->execute([
            $association_id,
            $stable_id,
            $method_id,
            $interactor1['protein_id'],
            $interactor1['start'],
            $interactor1['stop'],
            $interactor2['protein_id'],
            $interactor2['start'],
            $interactor2['stop'],
        ]);

        if ($select_description_sth->fetch()) {
            return true;
        }

        $select_description_sth->execute([
            $association_id,
            $stable_id,
            $method_id,
            $interactor2['protein_id'],
            $interactor2['start'],
            $interactor2['stop'],
            $interactor1['protein_id'],
            $interactor1['start'],
            $interactor1['stop'],
        ]);

        if ($select_description_sth->fetch()) {
            return true;
        }

        return false;
    }

    private function insertFirstVersion(int $association_id, int $method_id, array $interactor1, array $interactor2): StoreDescriptionResult
    {
        $insert_description_sth = $this->pdo->prepare(self::INSERT_DESCRIPTION_SQL);

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
                $inserted = false;
            }
        }

        if (!$inserted) {
            $this->pdo->rollback();

            return StoreDescriptionResult::firstVersionFailure();
        }

        $this->pdo->commit();

        $description['id'] = (int) $this->pdo->lastInsertId();

        return StoreDescriptionResult::success($description);
    }

    private function insertNewVersion(int $association_id, string $stable_id, int $method_id, array $interactor1, array $interactor2): StoreDescriptionResult
    {
        $update_descriptions_sth = $this->pdo->prepare(self::UPDATE_DESCRIPTIONS_SQL);
        $select_max_version_sth = $this->pdo->prepare(self::SELECT_MAX_VERSION_SQL);
        $insert_description_sth = $this->pdo->prepare(self::INSERT_DESCRIPTION_SQL);

        $this->pdo->beginTransaction();

        $update_descriptions_sth->execute([$stable_id]);

        $select_max_version_sth->execute([$stable_id]);

        $max_version = $select_max_version_sth->fetch(\PDO::FETCH_COLUMN) ?? 0;

        try {
            $insert_description_sth->execute([
                $stable_id,
                $max_version + 1,
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
            $this->pdo->rollback();

            return StoreDescriptionResult::newVersionFailure();
        }

        $this->pdo->commit();

        $description['id'] = (int) $this->pdo->lastInsertId();

        return StoreDescriptionResult::success($description);
    }
}
