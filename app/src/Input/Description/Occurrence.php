<?php

declare(strict_types=1);

namespace App\Input\Description;

use Quanta\Validation;
use Quanta\Validation\Factory;
use Quanta\Validation\AbstractInput;

final class Occurrence extends AbstractInput implements \JsonSerializable
{
    protected static function validation(Factory $factory, Validation $v): Factory
    {
        return $factory->validation(
            $v->rule(Coordinates::class),
            $v->key('identity')->float(Identity::class),
        );
    }

    public function __construct(
        public readonly Coordinates $coordinates,
        public readonly Identity $identity,
    ) {
    }

    /**
     * @return array{0: int, 1: int}
     */
    public function xy(): array
    {
        return [$this->coordinates->start->value(), $this->coordinates->stop->value()];
    }

    public function length(): int
    {
        return $this->coordinates->stop->value() - $this->coordinates->start->value() + 1;
    }

    public function jsonSerialize(): array
    {
        return [
            'start' => $this->coordinates->start,
            'stop' => $this->coordinates->stop,
            'identity' => $this->identity,
        ];
    }
}
