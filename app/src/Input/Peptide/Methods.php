<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use Quanta\Validation;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;

final class Methods extends AbstractInput
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->key('expression')->string(),
            $v->key('interaction')->string(),
        );
    }

    public function __construct(
        public readonly string $expression,
        public readonly string $interaction,
    ) {
    }

    public function data(): array
    {
        return [
            'expression' => $this->expression,
            'interaction' => $this->interaction,
        ];
    }
}
