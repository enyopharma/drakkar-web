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
use Quanta\Validation\Rules\Matching;
use Quanta\Validation\Rules\GreaterThanEqual;

final class AlignmentInput
{
    const MIN_LENGTH = 4;

    const SEQUENCE_PATTERN = '/^[a-zA-Z]*$/';

    private string $sequence;

    private array $isoforms;

    public static function factory(DataSource $source): callable
    {
        $factory = function (string $sequence, array $isoforms) {
            return new self(strtoupper($sequence), ...array_values($isoforms));
        };

        $is_arr = new Guard(new OfType('array'));
        $is_str = new Guard(new OfType('string'));
        $is_gte1 = new Guard(new GreaterThanEqual(1));
        $is_gte_min = new Guard(new GreaterThanEqual(self::MIN_LENGTH));
        $is_sequence = new Guard(new Matching(self::SEQUENCE_PATTERN));
        $is_isoform = IsoformInput::factory($source);
        $is_valid = new Guard(
            fn ($x) => $x->areSameProtein($source),
            fn ($x) => $x->areIsoformsUnique(),
            fn ($x) => $x->areLengthsValid($source),
        );

        $validation = new Validation($factory,
            Field::required('sequence', $is_str, $is_gte_min, $is_sequence)->focus(),
            Field::required('isoforms', $is_arr, $is_gte1, Map::merged($is_arr, $is_isoform))->focus(),
        );

        return new Bound($validation, $is_valid);
    }

    private function __construct(
        string $sequence,
        IsoformInput $isoform,
        IsoformInput ...$isoforms
    ) {
        $this->sequence = $sequence;
        $this->isoforms = [$isoform, ...$isoforms];
    }

    private function areSameProtein(DataSource $source): array
    {
        $map = [];

        foreach ($this->isoforms as $isoform) {
            $accession = $isoform->accession();

            $data = $source->isoform($accession);

            if ($data) {
                $map[$data['protein']]++;
            }
        }

        if (count($map) == 1) {
            return [];
        }

        return [
            new Error('all isoforms must be the same protein'),
        ];
    }

    private function areIsoformsUnique(): array
    {
        $map = [];

        foreach ($this->isoforms as $isoform) {
            $accession = $isoform->accession();

            $map[$accession]++;
        }

        $errors = [];

        foreach ($map as $accession => $n) {
            if ($n > 1) {
                $errors[] = new Error(
                    sprintf('isoform [%s] must be present only once', $accession)
                );
            }
        }

        return $errors;
    }

    private function areLengthsValid(DataSource $source): array
    {
        $errors = [];

        $length = strlen($this->sequence);

        foreach ($this->isoforms as $isoform) {
            $accession = $isoform->accession();

            $data = $source->isoform($accession);

            if ($data && $length > strlen($data['sequence'])) {
                $errors[] = new Error(
                    sprintf('isoform [%s] sequence must be greater than or equal to sequence', $accession)
                );
            }

            foreach ($isoform->occurrences() as $o => $occurrence) {
                if ($length != $occurrence->stop() - $occurrence->start() + 1) {
                    $errors[] = new Error(
                        vsprintf('occurrence [%s][%s] must have the same length as the sequence', [
                            $accession,
                            (string) $o,
                        ]
                    ));
                }
            }
        }

        return $errors;
    }

    public function sequence(): string
    {
        return $this->sequence;
    }

    public function isoforms(): array
    {
        return $this->isoforms;
    }

    public function data(): array
    {
        return [
            'sequence' => $this->sequence,
            'isoforms' => array_map(fn ($x) => $x->data(), $this->isoforms),
        ];
    }
}
