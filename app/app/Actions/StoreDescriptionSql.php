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
        SELECT * FROM proteins WHERE accession = ?
    SQL;

    const SELECT_DESCRIPTION_SQL = <<<SQL
        SELECT d.*
        FROM descriptions AS d, interactors AS i1, interactors AS i2
        WHERE i1.id = d.interactor1_id AND i2.id = d.interactor2_id
        AND d.association_id = ?
        AND d.method_id = ?
        AND i1.protein_id = ?
        AND i2.protein_id = ?
        AND i2.start = ?
        AND i2.stop = ?
        AND deleted_at IS NULL
    SQL;

    const INSERT_INTERACTOR_SQL = <<<SQL
        INSERT INTO interactors
        (protein_id, name, start, stop, mapping)
        VALUES (?, ?, ?, ?, ?)
    SQL;

    const INSERT_DESCRIPTION_SQL = <<<SQL
        INSERT INTO descriptions
        (association_id, method_id, interactor1_id, interactor2_id, stable_id)
        VALUES (?, ?, ?, ?, ?)
    SQL;

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(DescriptionInput $input): StoreDescriptionResult
    {
        // exctract data from the input.
        $association = $input->association();
        $method = $input->method();
        $interactor1 = $input->interactor1();
        $interactor2 = $input->interactor2();

        // prepare the queries.
        $select_method_sth = $this->pdo->prepare(self::SELECT_METHOD_SQL);
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);
        $insert_interactor_sth = $this->pdo->prepare(self::INSERT_INTERACTOR_SQL);
        $insert_description_sth = $this->pdo->prepare(self::INSERT_DESCRIPTION_SQL);

        // Select method id, protein1 id, protein2 id.
        $select_method_sth->execute([$method['psimi_id']]);
        $method = $select_method_sth->fetch();

        $select_protein_sth->execute([$interactor1['protein']['accession']]);
        $protein1 = $select_protein_sth->fetch();

        $select_protein_sth->execute([$interactor2['protein']['accession']]);
        $protein2 = $select_protein_sth->fetch();

        // ensure description does not exists (both directions).
        $select_description_sth->execute([
            $association->id(),
            $method['id'],
            $protein1['id'],
            $protein2['id'],
            $interactor2['start'],
            $interactor2['stop'],
        ]);

        if ($select_description_sth->fetch()) {
            return StoreDescriptionResult::descriptionAlreadyExists();
        }

        $select_description_sth->execute([
            $association->id(),
            $method['id'],
            $protein2['id'],
            $protein1['id'],
            $interactor1['start'],
            $interactor1['stop'],
        ]);

        if ($select_description_sth->fetch()) {
            return StoreDescriptionResult::descriptionAlreadyExists();
        }

        // insert the description.
        $this->pdo->beginTransaction();

        $insert_interactor_sth->execute([
            $protein1['id'],
            $interactor1['name'],
            $interactor1['start'],
            $interactor1['stop'],
            json_encode($interactor1['mapping']),
        ]);

        $interactor1['id'] = $this->pdo->lastInsertId();

        $insert_interactor_sth->execute([
            $protein2['id'],
            $interactor2['name'],
            $interactor2['start'],
            $interactor2['stop'],
            json_encode($interactor2['mapping']),
        ]);

        $interactor2['id'] = $this->pdo->lastInsertId();

        $tries = 0;
        $inserted = false;

        while (! $inserted && $tries < 10) {
            try {
                $tries++;

                $inserted = $insert_description_sth->execute([
                    $association->id(),
                    $method['id'],
                    $interactor1['id'],
                    $interactor2['id'],
                    'EY' . strtoupper(bin2hex(random_bytes(4))),
                ]);
            }
            catch (\PDOException $e) {
                $inserted = false;
            }
        }

        if (! $inserted) {
            $this->pdo->rollback();

            return StoreDescriptionResult::stableIdFailure();
        }

        $this->pdo->commit();

        $description['id'] = (int) $this->pdo->lastInsertId();

        return StoreDescriptionResult::success($description);
    }
}
