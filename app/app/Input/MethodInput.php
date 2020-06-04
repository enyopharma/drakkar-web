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

final class MethodInput
{
    const PSIMI_ID_PATTERN = '/^MI:[0-9]{4}$/';

    private string $psimi_id;

    public static function factory(DataSource $source): callable
    {
        $factory = fn (...$xs) => new self(...$xs);

        $is_str = new Guard(new OfType('string'));
        $is_not_empty = new Guard(new NotEmpty);
        $is_psimi_id = new Guard(new Matching(self::PSIMI_ID_PATTERN));
        $is_existing = new Guard(fn ($x) => $x->isExisting($source));

        $validation = new Validation($factory,
            Field::required('psimi_id', $is_str, $is_not_empty, $is_psimi_id)->focus(),
        );

        return new Bound($validation, $is_existing);
    }

    private function __construct(string $psimi_id)
    {
        $this->psimi_id = $psimi_id;
    }

    private function isExisting(DataSource $source): array
    {
        $data = $source->method($this->psimi_id);

        if ($data) {
            return [];
        }

        return [
            new Error(sprintf('no method with psimi id %s', $this->psimi_id))
        ];
    }

    public function data(): array
    {
        return [
            'psimi_id' => $this->psimi_id,
        ];
    }
}
