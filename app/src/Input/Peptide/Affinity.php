<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use Quanta\Validation;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;

final class Affinity extends AbstractInput
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->key('type')->string(),
            $v->key('value')->nullable()->float(),
            $v->key('unit')->string(),
        );
    }

    public function __construct(
        public readonly string $type,
        public readonly ?float $value,
        public readonly string $unit,
    ) {
    }

    public function data(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'unit' => $this->unit,
        ];
    }
}
