<?php

declare(strict_types=1);

namespace App\ReadModel;

final class MethodSql implements MethodInterface
{
    private \PDO $pdo;

    private int $id;

    private string $psimi_id;

    private array $data;

    public function __construct(\PDO $pdo, int $id, string $psimi_id, array $data = [])
    {
        $this->pdo = $pdo;
        $this->id = $id;
        $this->psimi_id = $psimi_id;
        $this->data = $data;
    }

    public function data(): array
    {
        $data = [
            'psimi_id' => $this->psimi_id,
        ];

        return $data + $this->data;
    }
}
