<?php

declare(strict_types=1);

namespace App\ReadModel;

final class DescriptionSql implements DescriptionInterface
{
    private \PDO $pdo;

    private int $run_id;

    private int $pmid;

    private int $id;

    private array $data;

    public function __construct(\PDO $pdo, int $run_id, int $pmid, int $id, array $data = [])
    {
        $this->pdo = $pdo;
        $this->run_id = $run_id;
        $this->pmid = $pmid;
        $this->id = $id;
        $this->data = $data;
    }

    public function data(): array
    {
        return [
            'id' => $this->id,
            'pmid' => $this->pmid,
            'run_id' => $this->run_id,
        ] + $this->data;
    }
}
