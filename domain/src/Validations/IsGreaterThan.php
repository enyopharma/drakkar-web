<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class IsGreaterThan
{
    private $threshold;

    public function __construct(int $threshold)
    {
        $this->threshold = $threshold;
    }

    public function __invoke($value): InputInterface
    {
        if ((is_int($value) || is_float($value)) && $value < $this->threshold) {
            return new Failure(new Error('%%s => must be greater than or equal to %s', $this->threshold));
        }

        if (is_array($value) && ($nb = count($value)) < $this->threshold) {
            return new Failure(new Error(
                '%%s => must have at least %s %s (%s given)',
                $this->threshold,
                $this->threshold > 1 ? 'values' : 'value',
                $nb,
            ));
        }

        if (is_string($value) && ($nb = strlen($value)) < $this->threshold) {
            return new Failure(new Error(
                '%%s => must have at least %s %s (%s given)',
                $this->threshold,
                $this->threshold > 1 ? 'characters' : 'character',
                $nb,
            ));
        }

        return new Success($value);
    }
}
