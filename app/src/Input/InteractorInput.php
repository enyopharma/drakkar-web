<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation;
use Quanta\Validation\Map;
use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\Field;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Rules\OfType;

use App\Assertions\ProteinType;

final class InteractorInput
{
    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT p.*, p.sequences->>p.accession AS sequence, v.current_version AS version
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

    const NAME_PATTERN = '/^[^\s]+$/';

    private int $protein_id;

    private string $name;

    private int $start;

    private int $stop;

    private array $alignments;

    public static function factory(): callable
    {
        $factory = function ($protein_id, $name, $start, $stop, array $alignments) {
            return self::from($protein_id, $name, $start, $stop, ...array_values($alignments));
        };

        $is_arr = new Guard(new OfType('array'));
        $is_str = new Guard(new OfType('string'));
        $is_int = new Guard(new OfType('int'));
        $alignment = AlignmentInput::factory();

        return new Validation($factory,
            Field::required('protein_id', $is_int)->focus(),
            Field::required('name', $is_str)->focus(),
            Field::required('start', $is_int)->focus(),
            Field::required('stop', $is_int)->focus(),
            Field::required('mapping', $is_arr, Map::merged($is_arr, $alignment))->focus(),
        );
    }

    public static function from(int $protein_id, string $name, int $start, int $stop, AlignmentInput ...$alignments): self
    {
        $input = new self($protein_id, $name, $start, $stop, ...$alignments);

        $errors = [
            ...array_map(fn ($e) => $e->nest('protein_id'), $input->validateProteinId()),
            ...array_map(fn ($e) => $e->nest('name'), $input->validateName()),
            ...$input->validateCoordinates(),
        ];

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(int $protein_id, string $name, int $start, int $stop, AlignmentInput ...$alignments)
    {
        $this->protein_id = $protein_id;
        $this->name = $name;
        $this->start = $start;
        $this->stop = $stop;
        $this->alignments = $alignments;
    }

    public function data(): array
    {
        return [
            'protein_id' => $this->protein_id,
            'name' => $this->name,
            'start' => $this->start,
            'stop' => $this->stop,
            'mapping' => array_map(fn ($a) => $a->data(), $this->alignments),
        ];
    }

    private function validateProteinId(): array
    {
        return $this->protein_id < 1
            ? [new Error('must be positive')]
            : [];
    }

    private function validateName(): array
    {
        return preg_match(self::NAME_PATTERN, $this->name) === 0
            ? [new Error('must match %s', self::NAME_PATTERN)]
            : [];
    }

    private function validateCoordinates(): array
    {
        $errors = [];

        if ($this->start < 1) {
            $errors[] = (new Error('must be positive'))->nest('start');
        }

        if ($this->stop < 1) {
            $errors[] = (new Error('must be positive'))->nest('stop');
        }

        if (count($errors) == 0 && $this->start > $this->stop) {
            $errors[] = new Error('start must be smaller than stop');
        }

        return $errors;
    }

    public function validateForDbAndType(\PDO $pdo, string $type): array
    {
        ProteinType::argument($type);

        $select_protein_sth = $pdo->prepare(self::SELECT_PROTEIN_SQL);

        $select_protein_sth->execute([$this->protein_id]);

        $protein = $select_protein_sth->fetch();

        if (!$protein) {
            return [(new Error('must exist'))->nest('protein_id')];
        }

        $errors = [];

        if ($protein['type'] != $type) {
            $errors[] = new Error(sprintf('must be a %s protein', $type == ProteinType::H ? 'human' : 'viral'));
        }

        if (is_null($protein['version'])) {
            $errors[] = new Error(sprintf('this version of protein %s is obsolete', $protein['accession']));
        }

        $es = $type == ProteinType::H
            ? $this->validateHProtein($protein)
            : $this->validateVProtein($pdo, $protein);

        $errors = [...$errors, ...$es];

        return count($errors) == 0
            ? array_map(fn ($e) => $e->nest('mapping'), $this->validateMapping($protein))
            : $errors;
    }

    private function validateHProtein(array $protein): array
    {
        if ($protein['name'] != $this->name) {
            return [new Error(vsprintf('invalid name \'%s\' for interactor (human, %s) - \'%s\' expected', [
                $this->name,
                $protein['accession'],
                $protein['name'],
            ]))];
        }

        if ($this->start != 1 || $this->stop != strlen($protein['sequence'])) {
            return [new Error(vsprintf('invalid coordinates [%s, %s] for interactor (human, %s) - [1, %s] expected', [
                $this->start,
                $this->stop,
                $protein['accession'],
                strlen($protein['sequence']),
            ]))];
        }

        return [];
    }

    private function validateVProtein(\PDO $pdo, array $protein): array
    {
        // validate coordinates.
        if ($this->stop > strlen($protein['sequence'])) {
            return [new Error('coordinates must be inside the protein sequence')];
        }

        // validate name/coordinates consistency.
        $select_name_sth = $pdo->prepare(self::SELECT_MATURE_NAME_SQL);

        $select_name_sth->execute([$this->protein_id, $this->start, $this->stop]);

        $data = $select_name_sth->fetch();

        if ($data && $data['name'] != $this->name) {
            return [
                new Error(vsprintf('invalid name \'%s\' for interactor (viral, %s, %s, %s) - \'%s\' expected', [
                    $this->name,
                    $protein['accession'],
                    $this->start,
                    $this->stop,
                    $data['name'],
                ])),
            ];
        }

        // validate coordinates/name consistency.
        $select_coordinates_sth = $pdo->prepare(self::SELECT_MATURE_COORDINATES_SQL);

        $select_coordinates_sth->execute([$this->protein_id, $this->name]);

        $data = $select_coordinates_sth->fetch();

        if ($data && ($data['start'] != $this->start || $data['stop'] != $this->stop)) {
            return [
                new Error(vsprintf('invalid coordinates [%s, %s] for interactor (viral, %s, %s) - [%s, %s] expected', [
                    $this->start,
                    $this->stop,
                    $protein['accession'],
                    $this->name,
                    $data['start'],
                    $data['stop'],
                ])),
            ];
        }

        return [];
    }

    private function validateMapping(array $protein): array
    {
        $errors = [];

        $sequences = array_map(fn ($a) => $a->sequence(), $this->alignments);

        if (count($sequences) > count(array_unique($sequences))) {
            $errors[] = (new Error('must be unique'))->nest('sequence');
        }

        $accession = $protein['accession'];
        $subjects = json_decode($protein['sequences'], true);

        $subjects[$accession] = substr($subjects[$accession], $this->start - 1, $this->stop - $this->start + 1);

        foreach ($this->alignments as $i => $alignment) {
            $errors = [...$errors, ...array_map(fn ($e) => $e->nest((string) $i), $alignment->validateForSubjects($subjects))];
        }

        return $errors;
    }
}
