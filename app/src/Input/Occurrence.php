<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\ArrayKey;
use App\Input\Validation\ArrayFactory;

final class Occurrence implements \JsonSerializable
{
    /**
     * @param mixed[] $data
     */
    public static function from(array $data): self
    {
        $factory = ArrayFactory::class(self::class)->validators(
            [Coordinates::class, 'from'],
            ArrayKey::required('identity')->float([Identity::class, 'from']),
        );

        return $factory($data);
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
        return [$this->coordinates->start->value, $this->coordinates->stop->value];
    }

    public function length(): int
    {
        return $this->coordinates->stop->value - $this->coordinates->start->value + 1;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'start' => $this->coordinates->start->value,
            'stop' => $this->coordinates->stop->value,
            'identity' => $this->identity->value,
        ];
    }
}
