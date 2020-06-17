<?php

declare(strict_types=1);

namespace App\Input;

final class Association
{
    private int $id;

    private string $type;

    public function __construct(int $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function type1(): string
    {
        return 'h';
    }

    public function type2(): string
    {
        return $this->type == 'hh' ? 'h' : 'v';
    }
}
