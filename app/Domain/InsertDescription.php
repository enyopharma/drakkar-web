<?php declare(strict_types=1);

namespace App\Domain;

final class InsertDescription
{
    const ASSOCIATION_NOT_FOUND = 0;
    const METHOD_NOT_FOUND = 1;
    const PROTEIN_NOT_FOUND = 2;
    const INTERACTOR_FORMAT_ERROR = 3;
    const INTERACTOR_TYPE_ERROR = 4;
    const INTERACTOR_NAME_ERROR = 5;
    const INTERACTOR_POS_ERROR = 6;
    const INTERACTOR_MAPPING_ERROR = 7;
    const NOT_UNIQUE = 8;

    const SELECT_ASSOCIATION_STH = <<<SQL
        SELECT r.type, a.*
        FROM runs AS r, associations AS a
        WHERE r.id = a.run_id
        AND r.id = ?
        AND a.pmid = ?
SQL;

    const SELECT_METHOD_STH = <<<SQL
        SELECT * FROM methods WHERE id = ?
SQL;

    const SELECT_PROTEIN_STH = <<<SQL
        SELECT id, type, accession, name FROM proteins WHERE id = ?
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

    public function __invoke(
        int $run_id,
        int $pmid,
        int $method_id,
        array $interactor1,
        array $interactor2
    ): DomainPayloadInterface {
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

        // ensure interactor arrays are valid.
        $interactor1 = $this->sanitizedInteractor($interactor1);
        $interactor2 = $this->sanitizedInteractor($interactor2);

        if (! $this->isInteractorValid($interactor1)) {
            return new DomainPayload(self::INTERACTOR_FORMAT_ERROR, ['n' => 1]);
        }

        if (! $this->isInteractorValid($interactor2)) {
            return new DomainPayload(self::INTERACTOR_FORMAT_ERROR, ['n' => 2]);
        }

        // ensure association exists.
        $select_association_sth->execute([$run_id, $pmid]);

        if (! $association = $select_association_sth->fetch()) {
            return new DomainPayload(self::ASSOCIATION_NOT_FOUND);
        }

        // ensure method exists.
        $select_method_sth->execute([$method_id]);

        if (! $method = $select_method_sth->fetch()) {
            return new DomainPayload(self::METHOD_NOT_FOUND);
        }

        // ensure protein of interactor1 exists.
        $select_protein_sth->execute([$interactor1['protein_id']]);

        if (! $protein1 = $select_protein_sth->fetch()) {
            return new DomainPayload(self::PROTEIN_NOT_FOUND, ['n' => 1]);
        }

        // ensure protein of interactor2 exists.
        $select_protein_sth->execute([$interactor2['protein_id']]);

        if (! $protein2 = $select_protein_sth->fetch()) {
            return new DomainPayload(self::PROTEIN_NOT_FOUND, ['n' => 2]);
        }

        // ensure type of interactor1 is human.
        if ($protein1['type'] != Protein::H) {
            return new DomainPayload(self::INTERACTOR_TYPE_ERROR, [
                'n' => 1, 'expected' => Protein::H,
            ]);
        }

        // ensure type of interactor2 is matching the curation run type.
        if ($association['type'] == Run::HH && $protein2['type'] == Protein::V) {
            return new DomainPayload(self::INTERACTOR_TYPE_ERROR, [
                'n' => 2, 'expected' => Protein::H,
            ]);
        }

        if ($association['type'] == Run::VH && $protein2['type'] == Protein::H) {
            return new DomainPayload(self::INTERACTOR_TYPE_ERROR, [
                'n' => 2, 'expected' => Protein::V,
            ]);
        }

        // ensure coordinates of interactor1 are valid.
        $select_sequence_sth->execute([
            $protein1['id'],
            $protein1['accession'],
        ]);

        $sequence = $select_sequence_sth->fetch();

        if ($interactor1['stop'] > strlen($sequence['sequence'])) {
            return new DomainPayload(self::INTERACTOR_POS_ERROR, ['n' => 1]);
        }

        // ensure coordinates of interactor2 are valid.
        $select_sequence_sth->execute([
            $protein2['id'],
            $protein2['accession'],
        ]);

        $sequence = $select_sequence_sth->fetch();

        if ($interactor2['stop'] > strlen($sequence['sequence'])) {
            return new DomainPayload(self::INTERACTOR_POS_ERROR, ['n' => 2]);
        }

        // ensure interactor1 name is consistant with start and stop.
        $select_interactor_name_sth->execute([
            $interactor1['protein_id'],
            $interactor1['start'],
            $interactor1['stop'],
        ]);

        if ($row = $select_interactor_name_sth->fetch()) {
            if ($row['name'] != $interactor1['name']) {
                return new DomainPayload(self::INTERACTOR_NAME_ERROR, [
                    'n' => 1, 'expected' => $row,
                ]);
            }
        }

        // ensure interactor2 name is consistant with start and stop.
        $select_interactor_name_sth->execute([
            $interactor2['protein_id'],
            $interactor2['start'],
            $interactor2['stop'],
        ]);

        if ($row = $select_interactor_name_sth->fetch()) {
            if ($row['name'] != $interactor2['name']) {
                return new DomainPayload(self::INTERACTOR_NAME_ERROR, [
                    'n' => 2, 'expected' => $row,
                ]);
            }
        }

        // ensure interactor1 start and stop is consistent with name.
        $select_interactor_pos_sth->execute([
            $interactor1['protein_id'],
            $interactor1['name'],
        ]);

        if ($row = $select_interactor_pos_sth->fetch()) {
            if ($row['start'] != $interactor1['start'] || $row['stop'] != $interactor1['stop']) {
                return new DomainPayload(self::INTERACTOR_POS_ERROR, [
                    'n' => 1, 'expected' => $row,
                ]);
            }
        }

        // ensure interactor2 start and stop is consistent with name.
        $select_interactor_pos_sth->execute([
            $interactor2['protein_id'],
            $interactor2['name'],
        ]);

        if ($row = $select_interactor_pos_sth->fetch()) {
            if ($row['start'] != $interactor2['start'] || $row['stop'] != $interactor2['stop']) {
                return new DomainPayload(self::INTERACTOR_POS_ERROR, [
                    'n' => 2, 'expected' => $row,
                ]);
            }
        }

        // ensure mapping of interactor1 is valid.
        foreach ($interactor1['mapping'] as $accession => $mapping) {
            $select_sequence_sth->execute([
                $interactor1['protein_id'],
                $accession,
            ]);

            if (! $sequence = $select_sequence_sth->fetch()) {
                return new DomainPayload(self::INTERACTOR_MAPPING_ERROR, ['n' => 1]);
            }

            foreach ($mapping as $alignment) {
                if ($alignment['stop'] > strlen($sequence['sequence'])) {
                    return new DomainPayload(self::INTERACTOR_MAPPING_ERROR, ['n' => 1]);
                }
            }
        }

        // ensure mapping of interactor2 is valid.
        foreach ($interactor2['mapping'] as $accession => $mapping) {
            $select_sequence_sth->execute([
                $interactor2['protein_id'],
                $accession,
            ]);

            if (! $sequence = $select_sequence_sth->fetch()) {
                return new DomainPayload(self::INTERACTOR_MAPPING_ERROR, ['n' => 2]);
            }

            foreach ($mapping as $alignment) {
                if ($alignment['stop'] > strlen($sequence['sequence'])) {
                    return new DomainPayload(self::INTERACTOR_MAPPING_ERROR, ['n' => 2]);
                }
            }
        }

        // ensure description does not exists.
        $select_description_sth->execute([
            $association['id'],
            $method['id'],
            $interactor1['protein_id'],
            $interactor2['protein_id'],
            $interactor2['start'],
            $interactor2['stop'],
        ]);

        if ($description = $select_description_sth->fetch()) {
            return new DomainPayload(self::NOT_UNIQUE);
        }

        // insert the description.
        $this->pdo->beginTransaction();

        $insert_interactor_sth->execute([
            $interactor1['protein_id'],
            $interactor1['name'],
            $interactor1['start'],
            $interactor1['stop'],
            json_encode($interactor1['mapping']),
        ]);

        $interactor1['id'] = $this->pdo->lastInsertId();

        $insert_interactor_sth->execute([
            $interactor2['protein_id'],
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

    private function sanitizedInteractor(array $interactor): array
    {
        return [
            'protein_id' => (int) $interactor['protein_id'] ?? 0,
            'name' => (string) $interactor['name'] ?? '',
            'start' => (int) $interactor['start'] ?? 0,
            'stop' => (int) $interactor['stop'] ?? 0,
            'mapping' => array_map(function  ($mapping) {
                return array_map(function ($alignment) {
                    return [
                        'start' => (int) $alignment['start'] ?? 0,
                        'stop' => (int) $alignment['stop'] ?? 0,
                        'sequence' => (string) $alignment['sequence'] ?? '',
                    ];
                }, $mapping);
            }, (array) $interactor['mapping'] ?? []),
        ];
    }

    private function isInteractorValid(array $interactor): bool
    {
        if ($interactor['protein_id'] < 1) {
            return false;
        }

        if ($interactor['name'] == '' || strlen($interactor['name']) > 32) {
            return false;
        }

        if ($interactor['start'] < 1) {
            return false;
        }

        if ($interactor['stop'] < $interactor['start']) {
            return false;
        }

        if (array_filter($interactor['mapping'], 'is_array') < count($interactor['mapping'])) {
            return false;
        }

        foreach ($interactor['mapping'] as $mapping) {
            foreach ($mapping as $alignment) {
                if ($alignment['start'] < 1) {
                    return false;
                }

                if ($alignment['stop'] < $alignment['start']) {
                    return false;
                }

                if ($alignment['sequence'] == '') {
                    return false;
                }

                if (preg_match('/^[GAVLIMFWPSTCYNQDEKRH]+$/i', $alignment['sequence']) !== 1) {
                    return false;
                }
            }
        }

        return true;
    }
}
