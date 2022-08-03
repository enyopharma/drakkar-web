<?php

declare(strict_types=1);

namespace App\Input\Peptide;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayInput;
use App\Input\Validation\ArrayFactory;

final class Methods extends ArrayInput
{
    protected static function validation(ArrayFactory $factory): ArrayFactory
    {
        return $factory->validators(
            ArrayKey::required('expression')->string(),
            ArrayKey::required('interaction')->string(),
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
