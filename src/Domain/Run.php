<?php

declare(strict_types=1);

namespace Domain;

final class Run implements ResourceInterface
{
    const HH = 'hh';
    const VH = 'vh';

    const TYPES = [
        self::HH,
        self::VH,
    ];

    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function id(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
