<?php

declare(strict_types=1);

namespace App\Input;

use App\Assertions\RunType;
use App\Assertions\ProteinType;

final class Association
{
    private int $id;

    private string $type;

    public function __construct(int $id, string $type)
    {
        RunType::argument($type);

        $this->id = $id;
        $this->type = $type;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function type1(): string
    {
        return ProteinType::H;
    }

    public function type2(): string
    {
        return $this->type == RunType::HH
            ? ProteinType::H
            : ProteinType::V;
    }
}
