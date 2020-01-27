<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class MethodSql implements MethodInterface
{
    private $pdo;

    private $id;

    private $psimi_id;

    private $data;

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
