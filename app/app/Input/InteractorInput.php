<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation;
use Quanta\Validation\Map;
use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\Bound;
use Quanta\Validation\Field;
use Quanta\Validation\Rules\OfType;
use Quanta\Validation\Rules\NotEmpty;
use Quanta\Validation\Rules\Matching;
use Quanta\Validation\Rules\GreaterThanEqual;

final class InteractorInput
{
    const NAME_PATTERN = '/^[^\s]+$/';

    private ProteinInput $protein;

    private string $name;

    private int $start;

    private int $stop;

    private array $alignments;

    public static function factory(DataSource $source): callable
    {
        $factory = function ($protein, $name, $start, $stop, array $alignments) {
            return new self($protein, $name, $start, $stop, ...array_values($alignments));
        };

        $is_arr = new Guard(new OfType('array'));
        $is_str = new Guard(new OfType('string'));
        $is_int = new Guard(new OfType('int'));
        $is_gte1 = new Guard(new GreaterThanEqual(1));
        $is_not_empty = new Guard(new NotEmpty);
        $is_name = new Guard(new Matching(self::NAME_PATTERN));
        $is_protein = ProteinInput::factory($source);
        $is_alignment = AlignmentInput::factory($source);
        $is_valid = new Guard(
            fn ($x) => $x->areCoordinatesValid($source),
            fn ($x) => $x->areAlignmentsOnProtein($source),
            fn ($x) => $x->areAlignmentsUnique(),
        );
        $is_name_consistent = new Guard(fn ($x) => $x->isNameConsistent($source));
        $are_coordinates_consistent = new Guard(fn ($x) => $x->areCoordinatesConsistent($source));
        $are_alignments_valid = new Guard(fn ($x) => $x->areAlignmentsValid());

        $validation = new Validation($factory,
            Field::required('protein', $is_arr, $is_protein)->focus(),
            Field::required('name', $is_str, $is_not_empty, $is_name)->focus(),
            Field::required('start', $is_int, $is_gte1)->focus(),
            Field::required('stop', $is_int, $is_gte1)->focus(),
            Field::required('mapping', $is_arr, Map::merged($is_arr, $is_alignment))->focus(),
        );

        return new Bound(
            $validation,
            $is_valid,
            $is_name_consistent,
            $are_coordinates_consistent,
            $are_alignments_valid,
        );
    }

    private function __construct(
        ProteinInput $protein,
        string $name,
        int $start,
        int $stop,
        AlignmentInput ...$alignments
    ) {
        $this->protein = $protein;
        $this->name = $name;
        $this->start = $start;
        $this->stop = $stop;
        $this->alignments = $alignments;
    }

    private function areAlignmentsUnique(): array
    {
        $map = [];

        foreach ($this->alignments as $alignment) {
            $sequence = $alignment->sequence();

            $map[$sequence]++;
        }

        $errors = [];

        foreach ($map as $sequence => $n) {
            if ($n > 1) {
                $errors[] = new Error('alignments must be present only once');
            }
        }

        return $errors;
    }

    private function areAlignmentsOnProtein(DataSource $source): array
    {
        $protein = $this->protein->accession();

        foreach ($this->alignments as $alignment) {
            foreach ($alignment->isoforms() as $isoform) {
                $accession = $isoform->accession();

                $data = $source->isoform($accession);

                if ($data && $protein != $data['protein']) {
                    return [
                        new Error(
                            sprintf('all alignments must be on protein [%s]', $protein)
                        ),
                    ];
                }
            }
        }

        return [];
    }

    private function areCoordinatesValid(DataSource $source): array
    {
        $errors = [];

        $accession = $this->protein->accession();

        $data = $source->protein($accession);

        if (!$data) return [];

        $length = strlen($data['sequence']);

        if ($this->start > $this->stop) {
            $errors[] = new Error('start must be less than stop');
        }

        if ($data['type'] == 'h' && ($this->start > 1 || $this->stop < $length)) {
            $errors[] = new Error('human interactor must be full length');
        }

        if ($this->stop > $length) {
            $errors[] = new Error('coordinates must be inside the protein');
        }

        return $errors;
    }

    private function isNameConsistent(DataSource $source): array
    {
        $accession = $this->protein->accession();

        $data = $source->name($accession, $this->start, $this->stop);

        if (!$data || $this->name == $data['name']) {
            return [];
        }

        return [
            new Error(
                vsprintf('invalid name %s for interactor (%s, %s, %s) - %s expected', [
                    $this->name,
                    $accession,
                    $this->start,
                    $this->stop,
                    $data['name'],
                ])
            ),
        ];
    }

    private function areCoordinatesConsistent(DataSource $source): array
    {
        $accession = $this->protein->accession();

        $data = $source->coordinates($accession, $this->name);

        if (!$data || ($this->start == $data['start'] && $this->stop == $data['stop'])) {
            return [];
        }

        return [
            new Error(
                vsprintf('invalid coordinates [%s - %s] for interactor (%s, %s) - [%s - %s] expected', [
                    $this->start,
                    $this->stop,
                    $accession,
                    $this->name,
                    $data['start'],
                    $data['stop'],
                ])
            )
        ];
    }

    private function areAlignmentsValid(): array
    {
        return [];
    }

    public function protein(): ProteinInput
    {
        return $this->protein;
    }

    public function data(): array
    {
        return [
            'protein' => $this->protein->data(),
            'name' => $this->name,
            'start' => $this->start,
            'stop' => $this->stop,
            'mapping' => array_map(fn ($x) => $x->data(), $this->alignments),
        ];
    }
}
