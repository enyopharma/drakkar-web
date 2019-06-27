<?php declare(strict_types=1);

namespace App\Domain;

final class InsertDescription
{
    const NOT_FOUND = 0;
    const UNPROCESSABLE = 1;

    const SELECT_ASSOCIATION_STH = <<<SQL
        SELECT r.type, a.*
        FROM runs AS r, associations AS a
        WHERE r.id = a.run_id
        AND r.id = ?
        AND a.pmid = ?
SQL;

    const SELECT_METHOD_STH = <<<SQL
        SELECT * FROM methods WHERE psimi_id = ?
SQL;

    const SELECT_PROTEIN_STH = <<<SQL
        SELECT id, type, name
        FROM proteins
        WHERE accession = ?
SQL;

    const SELECT_INTERACTOR_NAME_STH = <<<SQL
        SELECT name, start, stop
        FROM interactors
        WHERE protein_id = ?
        AND start = ?
        AND stop = ?
SQL;

    const SELECT_INTERACTOR_POS_STH = <<<SQL
        SELECT name, start, stop
        FROM interactors
        WHERE protein_id = ?
        AND name = ?
SQL;

    const SELECT_SEQUENCE_STH = <<<SQL
        SELECT sequence
        FROM sequences
        WHERE protein_id = ?
        AND accession = ?
SQL;

    const SELECT_DESCRIPTION_STH = <<<SQL
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

    const INSERT_INTERACTOR_STH = <<<SQL
        INSERT INTO interactors
        (protein_id, name, start, stop, mapping)
        VALUES (?, ?, ?, ?, ?)
SQL;

    const INSERT_DESCRIPTION_STH = <<<SQL
        INSERT INTO descriptions
        (association_id, method_id, interactor1_id, interactor2_id)
        VALUES (?, ?, ?, ?)
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(DescriptionInput $input): DomainPayloadInterface {
        try {
            $run = $input->run();
            $publication = $input->publication();
            $method = $input->method();
            $interactor1 = $input->interactor1();
            $interactor2 = $input->interactor2();
        }

        catch (\UnexpectedValueException $e) {
            return new DomainPayload(self::UNPROCESSABLE, ['reason' => $e->getMessage()]);
        }

        // prepare the queries.
        $select_association_sth = $this->pdo->prepare(self::SELECT_ASSOCIATION_STH);
        $select_method_sth = $this->pdo->prepare(self::SELECT_METHOD_STH);
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_STH);
        $select_interactor_name_sth = $this->pdo->prepare(self::SELECT_INTERACTOR_NAME_STH);
        $select_interactor_pos_sth = $this->pdo->prepare(self::SELECT_INTERACTOR_POS_STH);
        $select_sequence_sth = $this->pdo->prepare(self::SELECT_SEQUENCE_STH);
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_STH);
        $insert_interactor_sth = $this->pdo->prepare(self::INSERT_INTERACTOR_STH);
        $insert_description_sth = $this->pdo->prepare(self::INSERT_DESCRIPTION_STH);

        // ensure association exists.
        $select_association_sth->execute([$run['id'], $publication['pmid']]);

        if (! $association = $select_association_sth->fetch()) {
            return $this->notfound('Association not found');
        }

        // ensure method exists.
        $select_method_sth->execute([$method['psimi_id']]);

        if (! $method = $select_method_sth->fetch()) {
            return $this->unprocessable('Method not found');
        }

        // ensure protein of interactor1 exists.
        $select_protein_sth->execute([$interactor1['protein']['accession']]);

        if (! $protein1 = $select_protein_sth->fetch()) {
            return $this->unprocessable('Interactor 1 protein not found');
        }

        // ensure protein of interactor2 exists.
        $select_protein_sth->execute([$interactor2['protein']['accession']]);

        if (! $protein2 = $select_protein_sth->fetch()) {
            return $this->unprocessable('Interactor 2 protein not found');
        }

        // ensure type of interactor1 is human.
        if ($protein1['type'] != Protein::H) {
            return $this->unprocessable('Interactor 1 protein must be a human protein');
        }

        // ensure type of interactor2 is matching the curation run type.
        if ($association['type'] == Run::HH && $protein2['type'] == Protein::V) {
            return $this->unprocessable('Interactor 2 protein must be a human protein');
        }

        if ($association['type'] == Run::VH && $protein2['type'] == Protein::H) {
            return $this->unprocessable('Interactor 2 protein must be a viral protein');
        }

        // ensure coordinates of interactor1 are valid.
        $select_sequence_sth->execute([$protein1['id'], $interactor1['protein']['accession']]);

        $sequence = $select_sequence_sth->fetch();

        if ($interactor1['stop'] > strlen($sequence['sequence'])) {
            return $this->unprocessable('Interactor 1 stop is greater than the protein sequence length');
        }

        // ensure coordinates of interactor2 are valid.
        $select_sequence_sth->execute([$protein2['id'], $interactor2['protein']['accession']]);

        $sequence = $select_sequence_sth->fetch();

        if ($interactor2['stop'] > strlen($sequence['sequence'])) {
            return $this->unprocessable('Interactor 2 stop is greater than the protein sequence length');
        }

        // ensure interactor1 name is consistant with start and stop.
        $select_interactor_name_sth->execute([
            $protein1['id'],
            $interactor1['start'],
            $interactor1['stop'],
        ]);

        if ($row = $select_interactor_name_sth->fetch()) {
            if ($row['name'] != $interactor1['name']) {
                return $this->unprocessable('Interactor 1 name is not valid');
            }
        }

        // ensure interactor2 name is consistant with start and stop.
        $select_interactor_name_sth->execute([
            $protein2['id'],
            $interactor2['start'],
            $interactor2['stop'],
        ]);

        if ($row = $select_interactor_name_sth->fetch()) {
            if ($row['name'] != $interactor2['name']) {
                return $this->unprocessable('Interactor 2 name is not valid');
            }
        }

        // ensure interactor1 start and stop is consistent with name.
        $select_interactor_pos_sth->execute([$protein1['id'], $interactor1['name']]);

        if ($row = $select_interactor_pos_sth->fetch()) {
            if ($row['start'] != $interactor1['start'] || $row['stop'] != $interactor1['stop']) {
                return $this->unprocessable('Interactor 1 coordinates are not valid');
            }
        }

        // ensure interactor2 start and stop is consistent with name.
        $select_interactor_pos_sth->execute([
            $protein2['id'],
            $interactor2['name'],
        ]);

        if ($row = $select_interactor_pos_sth->fetch()) {
            if ($row['start'] != $interactor2['start'] || $row['stop'] != $interactor2['stop']) {
                return $this->unprocessable('Interactor 2 coordinates are not valid');
            }
        }

        // ensure mapping of interactor1 is valid.
        foreach ($interactor1['mapping'] as $alignment) {
            foreach ($alignment['isoforms'] as $isoform) {
                $select_sequence_sth->execute([$protein1['id'], $isoform['accession']]);

                if (! $sequence = $select_sequence_sth->fetch()) {
                    return $this->unprocessable('Interactor 1 mapping is not valid');
                }

                foreach ($isoform['occurrences'] as $occurrence) {
                    if ($occurrence['stop'] > strlen($sequence['sequence'])) {
                        return $this->unprocessable('Interactor 1 mapping is not valid');
                    }
                }
            }
        }

        // ensure mapping of interactor2 is valid.
        foreach ($interactor2['mapping'] as $alignment) {
            foreach ($alignment['isoforms'] as $isoform) {
                $select_sequence_sth->execute([$protein2['id'], $isoform['accession']]);

                if (! $sequence = $select_sequence_sth->fetch()) {
                    return $this->unprocessable('Interactor 2 mapping is not valid');
                }

                foreach ($isoform['occurrences'] as $occurrence) {
                    if ($occurrence['stop'] > strlen($sequence['sequence'])) {
                        return $this->unprocessable('Interactor 2 mapping is not valid');
                    }
                }
            }
        }

        // ensure description does not exists.
        $select_description_sth->execute([
            $association['id'],
            $method['id'],
            $protein1['id'],
            $protein2['id'],
            $interactor2['start'],
            $interactor2['stop'],
        ]);

        if ($description = $select_description_sth->fetch()) {
            return $this->unprocessable('Description already exists');
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

        $insert_description_sth->execute([
            $association['id'],
            $method['id'],
            $interactor1['id'],
            $interactor2['id'],
        ]);

        $this->pdo->commit();

        $description['id'] = $this->pdo->lastInsertId();

        return new DomainSuccess(['description' => $description]);
    }

    private function notfound(string $reason): DomainPayloadInterface
    {
        return new DomainPayload(self::NOT_FOUND, ['reason' => $reason]);
    }

    private function unprocessable(string $reason): DomainPayloadInterface
    {
        return new DomainPayload(self::UNPROCESSABLE, ['reason' => $reason]);
    }
}
