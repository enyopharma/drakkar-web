<?php

declare(strict_types=1);

namespace App\Actions;

use App\Input\DescriptionInput;
use App\Assertions\RunType;
use App\Assertions\ProteinType;

final class StoreDescriptionSql implements StoreDescriptionInterface
{
    const SELECT_RUN_SQL = <<<SQL
        SELECT id, type FROM runs WHERE id = ?
    SQL;

    const SELECT_ASSOCIATION_SQL = <<<SQL
        SELECT id FROM associations WHERE run_id = ? AND pmid = ?
    SQL;

    const SELECT_ASSOCIATION_ID_SQL = <<<SQL
        SELECT association_id FROM descriptions WHERE stable_id = ?
    SQL;

    const SELECT_METHOD_SQL = <<<SQL
        SELECT id FROM methods WHERE id = ?
    SQL;

    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT p.*, p.sequences->>p.accession AS sequence, v.current_version
        FROM proteins AS p LEFT JOIN proteins_versions AS v ON p.accession = v.accession AND p.version = v.version
        WHERE p.id = ?
    SQL;

    const SELECT_MATURE_NAME_SQL = <<<SQL
        SELECT name2 AS name
        FROM descriptions
        WHERE protein2_id = ? AND start2 = ? AND stop2 = ? AND deleted_at IS NULL
        LIMIT 1
    SQL;

    const SELECT_MATURE_COORDINATES_SQL = <<<SQL
        SELECT start2 AS start, stop2 AS stop
        FROM descriptions
        WHERE protein2_id = ? AND name2 = ? AND deleted_at IS NULL
        LIMIT 1
    SQL;

    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT stable_id, mapping1, mapping2
        FROM descriptions
        WHERE association_id = ? AND method_id = ?
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

    public function __construct(
        private \PDO $pdo,
    ) {
    }

    public function store(int $run_id, int $pmid, DescriptionInput $input): StoreDescriptionResult
    {
        // exctract data from the input.
        $data = $input->data();

        $stable_id = $data['stable_id'];
        $method_id = $data['method_id'];
        $interactor1 = $data['interactor1'];
        $interactor2 = $data['interactor2'];

        // ensure run exists.
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);

        $select_run_sth->execute([$run_id]);

        if (!$run = $select_run_sth->fetch()) {
            return StoreDescriptionResult::runNotFound($run_id);
        }

        // ensure association exists.
        $select_association_sth = $this->pdo->prepare(self::SELECT_ASSOCIATION_SQL);

        $select_association_sth->execute([$run['id'], $pmid]);

        if (!$association = $select_association_sth->fetch()) {
            return StoreDescriptionResult::associationNotFound($run['id'], $pmid);
        }

        // collect errors.
        $errors = [
            ...$this->validateStableId($association['id'], $stable_id),
            ...$this->validateMethod($method_id),
            ...$this->validateInteractors($run['type'], $interactor1, $interactor2),
        ];

        if (count($errors) > 0) {
            return StoreDescriptionResult::inconsistentData(...$errors);
        }

        // ensure description does not exist.
        $same1 = $this->isExisting($association['id'], $stable_id, $method_id, $interactor1, $interactor2);
        $same2 = $this->isExisting($association['id'], $stable_id, $method_id, $interactor2, $interactor1);

        if ($same1 || $same2) {
            return StoreDescriptionResult::descriptionAlreadyExists();
        }

        // insert the description.
        return $stable_id == ''
            ? $this->insertFirstVersion($association['id'], $method_id, $interactor1, $interactor2)
            : $this->insertNewVersion($association['id'], $stable_id, $method_id, $interactor1, $interactor2);
    }

    private function validateStableId(int $association_id, string $stable_id): array
    {
        if ($stable_id == '') {
            return [];
        }

        $select_association_id_sth = $this->pdo->prepare(self::SELECT_ASSOCIATION_ID_SQL);

        $select_association_id_sth->execute([$stable_id]);

        if (!$row = $select_association_id_sth->fetch()) {
            return [sprintf('stable_id %s must exist', $stable_id)];
        }

        if ($association_id != $row['association_id']) {
            return [sprintf('stable_id %s must be associated to the same publication', $stable_id)];
        }

        return [];
    }

    private function validateMethod(int $method_id): array
    {
        $select_method_sth = $this->pdo->prepare(self::SELECT_METHOD_SQL);

        $select_method_sth->execute([$method_id]);

        if (!$method = $select_method_sth->fetch()) {
            return [sprintf('method with id %s must exist', $method_id)];
        }

        return [];
    }

    private function validateInteractors(string $run_type, array $interactor1, array $interactor2): array
    {
        $errors = [];

        $type1 = ProteinType::H;
        $type2 = $run_type == RunType::HH ? ProteinType::H : ProteinType::V;

        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);

        $select_protein_sth->execute([$interactor1['protein_id']]);

        if (!$protein1 = $select_protein_sth->fetch()) {
            $errors[] = sprintf('protein with id %s must exist', $interactor1['protein_id']);
        } else {
            $errors = [...$errors, ...$this->validateProtein(1, $type1, $protein1, $interactor1)];
        }

        $select_protein_sth->execute([$interactor2['protein_id']]);

        if (!$protein2 = $select_protein_sth->fetch()) {
            $errors[] = sprintf('protein with id %s must exist', $interactor2['protein_id']);
        } else {
            $errors = [...$errors, ...$this->validateProtein(2, $type2, $protein2, $interactor2)];
        }

        return $errors;
    }

    private function validateProtein(int $n, string $type, array $protein, array $interactor): array
    {
        $errors = [];

        if ($protein['type'] != $type) {
            $errors[] = sprintf('protein %s with id %s must be of type %s', $n, $protein['id'], $type);
        }

        if (!$protein['current_version']) {
            $errors[] = sprintf('protein %s with id %s is obsolete', $n, $protein['id']);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        return $type == ProteinType::H
            ? $this->validateHProtein($n, $protein, $interactor)
            : $this->validateVProtein($n, $protein, $interactor);
    }

    private function validateHProtein(int $n, array $protein, array $interactor): array
    {
        $errors = [];

        ['name' => $name, 'start' => $start, 'stop' => $stop] = $interactor;

        if ($protein['name'] != $name) {
            $errors[] = vsprintf('invalid name \'%s\' for interactor %s (human, %s) - \'%s\' expected', [
                $name,
                $n,
                $protein['accession'],
                $protein['name'],
            ]);
        }

        if ($start != 1 || $stop != strlen($protein['sequence'])) {
            $errors[] = vsprintf('invalid coordinates [%s, %s] for interactor %s (human, %s) - [1, %s] expected', [
                $start,
                $stop,
                $n,
                $protein['accession'],
                strlen($protein['sequence']),
            ]);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $sequences = json_decode($protein['sequences'], true);

        return $this->validateMapping($n, $sequences, $interactor['mapping']);
    }

    private function validateVProtein(int $n, array $protein, array $interactor): array
    {
        ['name' => $name, 'start' => $start, 'stop' => $stop] = $interactor;

        if ($stop > strlen($protein['sequence'])) {
            return [sprintf('coordinates of interactor %s must be inside the protein sequence', $n)];
        }

        $select_name_sth = $this->pdo->prepare(self::SELECT_MATURE_NAME_SQL);

        $select_name_sth->execute([$protein['id'], $start, $stop]);

        $data = $select_name_sth->fetch();

        if ($data && $data['name'] != $name) {
            return [
                vsprintf('invalid name \'%s\' for interactor %s (viral, %s, %s, %s) - \'%s\' expected', [
                    $name,
                    $n,
                    $protein['accession'],
                    $start,
                    $stop,
                    $data['name'],
                ]),
            ];
        }

        $select_coordinates_sth = $this->pdo->prepare(self::SELECT_MATURE_COORDINATES_SQL);

        $select_coordinates_sth->execute([$protein['id'], $name]);

        $data = $select_coordinates_sth->fetch();

        if ($data && ($data['start'] != $start || $data['stop'] != $stop)) {
            return [
                vsprintf('invalid coordinates [%s, %s] for interactor %s (viral, %s, %s) - [%s, %s] expected', [
                    $start,
                    $stop,
                    $n,
                    $protein['accession'],
                    $name,
                    $data['start'],
                    $data['stop'],
                ]),
            ];
        }

        $accession = $protein['accession'];
        $sequences = json_decode($protein['sequences'], true);

        $sequences[$accession] = substr($sequences[$accession], $start - 1, $stop - $start + 1);

        return $this->validateMapping($n, $sequences, $interactor['mapping']);
    }

    private function validateMapping(int $n, array $sequences, array $mapping): array
    {
        $errors = [];

        foreach ($mapping as $alignment) {
            foreach ($alignment['isoforms'] as $isoform) {
                $accession = $isoform['accession'];

                if (!array_key_exists($accession, $sequences)) {
                    $errors[] = sprintf('interactor %s - no isoform with accession %s', $n, $accession);
                } else {
                    $sequence = $sequences[$accession];

                    foreach ($isoform['occurrences'] as $occurrence) {
                        $stop = $occurrence['stop'];

                        if ($stop > strlen($sequence)) {
                            $errors[] = sprintf('interactor %s - mapping occurrence longer than sequence %s', $n, $accession);
                        }
                    }
                }
            }
        }

        return $errors;
    }

    private function isExisting(int $association_id, string $stable_id, int $method_id, array $interactor1, array $interactor2): bool
    {
        $select_descriptions_sth = $this->pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute([
            $association_id,
            $method_id,
            $interactor1['protein_id'],
            $interactor1['start'],
            $interactor1['stop'],
            $interactor2['protein_id'],
            $interactor2['start'],
            $interactor2['stop'],
        ]);

        while ($description = $select_descriptions_sth->fetch()) {
            if ($description['stable_id'] != $stable_id) {
                return true;
            }

            $same1 = $this->sameMapping(json_decode($description['mapping1'], true), $interactor1['mapping']);
            $same2 = $this->sameMapping(json_decode($description['mapping2'], true), $interactor2['mapping']);

            if ($same1 && $same2) {
                return true;
            }
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
                    json_encode($interactor1['mapping'], JSON_THROW_ON_ERROR),
                    $interactor2['protein_id'],
                    $interactor2['name'],
                    $interactor2['start'],
                    $interactor2['stop'],
                    json_encode($interactor2['mapping'], JSON_THROW_ON_ERROR),
                ]);
            } catch (\PDOException $e) {
                $inserted = false;
            }
        }

        if (!$inserted) {
            $this->pdo->rollback();

            return StoreDescriptionResult::firstVersionFailure();
        }

        $this->pdo->commit();

        $id = (int) $this->pdo->lastInsertId();

        return StoreDescriptionResult::success($id);
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
                json_encode($interactor1['mapping'], JSON_THROW_ON_ERROR),
                $interactor2['protein_id'],
                $interactor2['name'],
                $interactor2['start'],
                $interactor2['stop'],
                json_encode($interactor2['mapping'], JSON_THROW_ON_ERROR),
            ]);
        } catch (\PDOException $e) {
            $this->pdo->rollback();

            return StoreDescriptionResult::newVersionFailure();
        }

        $this->pdo->commit();

        $id = (int) $this->pdo->lastInsertId();

        return StoreDescriptionResult::success($id);
    }

    private function sameMapping(array $mapping1, array $mapping2): bool
    {
        $flat1 = iterator_to_array($this->flatAlignments($mapping1));
        $flat2 = iterator_to_array($this->flatAlignments($mapping2));

        if (count($flat1) != count($flat2)) return false;

        foreach ($flat1 as $hash => $isoforms) {
            if (!array_key_exists($hash, $flat2)) return false;

            if (count($isoforms) != count($flat2[$hash])) return false;

            foreach ($isoforms as $accession => $starts) {
                if (!array_key_exists($accession, $flat2[$hash])) return false;

                if (count($starts) != count($flat2[$hash][$accession])) return false;

                foreach ($starts as $start) {
                    if (!in_array($start, $flat2[$hash][$accession])) return false;
                }
            }
        }

        return true;
    }

    private function flatAlignments(array $alignments): \Generator
    {
        foreach ($alignments as ['sequence' => $sequence, 'isoforms' => $isoforms]) {
            yield md5($sequence) => iterator_to_array($this->flatIsoforms($isoforms));
        }
    }

    private function flatIsoforms(array $isoforms): \Generator
    {
        foreach ($isoforms as ['accession' => $accession, 'occurrences' => $occurrences]) {
            yield $accession => array_map(fn (array $o) => $o['start'], $occurrences);
        }
    }
}
