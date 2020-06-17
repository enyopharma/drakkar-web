<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\Bound;
use Quanta\Validation\Field;
use Quanta\Validation\Rules\OfType;
use Quanta\Validation\Rules\NotEmpty;
use Quanta\Validation\Rules\Matching;

final class ProteinInput
{
    const ACCESSION_PATTERN = '/^[A-Z0-9]+$/';

    private string $accession;

    public static function factory(DataSource $source): callable
    {
        $factory = fn (...$xs) => new self(...$xs);

        $is_str = new Guard(new OfType('string'));
        $is_not_empty = new Guard(new NotEmpty);
        $is_accession = new Guard(new Matching(self::ACCESSION_PATTERN));
        $is_existing = new Guard(fn ($x) => $x->isExisting($source));

        $validation = new Validation($factory,
            Field::required('accession', $is_str, $is_not_empty, $is_accession)->focus(),
        );

        return new Bound($validation, $is_existing);
    }

    private function __construct(string $accession)
    {
        $this->accession = $accession;
    }

    private function isExisting(DataSource $source): array
    {
        $data = $source->protein($this->accession);

        if ($data) {
            return [];
        }

        return [
            new Error(sprintf('no protein with accession %s', $this->accession)),
        ];
    }

    public function accession(): string
    {
        return $this->accession;
    }

    public function data(): array
    {
        return [
            'accession' => $this->accession,
        ];
    }
}
