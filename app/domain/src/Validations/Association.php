<?php

declare(strict_types=1);

namespace Domain\Validations;

use Domain\Run;
use Domain\Protein;

final class Association
{
    private $id;

    private $type;

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
        return Protein::H;
    }

    public function type2(): string
    {
        return $this->type == Run::HH ? Protein::H : Protein::V;
    }
}
