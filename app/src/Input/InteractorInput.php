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

    public static function factory(\PDO $pdo, string $type): callable
    {
        ProteinType::argument($type);

        $is_arr = new Guard(new OfType('array'));
        $is_str = new Guard(new OfType('string'));
        $is_int = new Guard(new OfType('int'));

        $factory = function ($protein_id, $name, $start, $stop, array $alignments) use ($pdo, $type) {
            return self::from($pdo, $type, $protein_id, $name, $start, $stop, ...array_values($alignments));
        };

        return new Validation($factory,
            Field::required('protein_id', $is_int)->focus(),
            Field::required('name', $is_str)->focus(),
            Field::required('start', $is_int)->focus(),
            Field::required('stop', $is_int)->focus(),
            Field::required('mapping', $is_arr, Map::merged($is_arr))->focus(),
        );
    }

    public static function from(\PDO $pdo, string $type, int $protein_id, string $name, int $start, int $stop, array ...$alignments): self
    {
        ProteinType::argument($type);

        $input = new self($protein_id, $name, $start, $stop, ...$alignments);

        return validated($input, ...$input->validate($pdo, $type));
    }

    private function __construct(int $protein_id, string $name, int $start, int $stop, array ...$alignments)
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
            'mapping' => $this->alignments,
        ];
    }

    private function validate(\PDO $pdo, string $type): array
    {
        $select_protein_sth = $pdo->prepare(self::SELECT_PROTEIN_SQL);

        $select_protein_sth->execute([$this->protein_id]);

        $protein = $select_protein_sth->fetch();

        if ($protein === false) {
            return [new Error('protein not found')];
        }

        return bound(
            $this->validateProtein($pdo, $type, $protein),
            nested('mapping', ...$this->validateAlignments($protein)),
            nested('mapping', ...$this->validateAlignmentsUniqueness()),
        );
    }

    private function validateProtein(\PDO $pdo, string $type, array $protein): array
    {
        if ($protein['type'] != $type) {
            return [new Error(sprintf('must be a %s protein', $type == ProteinType::H ? 'human' : 'viral'))];
        }

        if (is_null($protein['version'])) {
            return [new Error(vsprintf('This version of protein %s is obsolete', [
                $protein['accession'],
            ]))];
        }

        return $type == ProteinType::H
            ? $this->validateHProtein($protein)
            : $this->validateVProtein($pdo, $protein);
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
        // validate name and coordinates for a new mature protein.
        $errors = [];

        if (preg_match(self::NAME_PATTERN, $this->name) === 0) {
            $errors[] = new Error('name is not valid');
        }

        if ($this->start < 1) {
            $errors[] = new Error('start must be greater than 0');
        }

        if ($this->stop < 1) {
            $errors[] = new Error('stop must be greater than 0');
        }

        if ($this->start > $this->stop) {
            $errors[] = new Error('start must be greater than or equal to stop');
        }

        if ($protein && $this->stop > strlen($protein['sequence'])) {
            $errors[] = new Error('coordinates must be inside the protein sequence');
        }

        if (count($errors) > 0) return $errors;

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

    private function validateAlignments(array $protein): array
    {
        // format the sequences array.
        $accession = $protein['accession'];
        $sequences = json_decode($protein['sequences'], true);

        $sequences[$accession] = substr($sequences[$accession], $this->start - 1, $this->stop - $this->start + 1);

        $are_alignments = Map::merged(AlignmentInput::factory($sequences));

        return unpacked(fn () => $are_alignments($this->alignments));
    }

    private function validateAlignmentsUniqueness(): array
    {
        $seen = [];

        foreach ($this->alignments as ['sequence' => $sequence]) {
            $nb = $seen[$sequence] ?? 0;

            if ($nb == 1) {
                return [new Error('alignment sequence must be present only once')];
            }

            $seen[$sequence] = $nb + 1;
        }

        return [];
    }
}
