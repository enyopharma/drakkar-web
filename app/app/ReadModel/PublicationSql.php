<?php

declare(strict_types=1);

namespace App\ReadModel;

final class PublicationSql implements PublicationInterface
{
    private \PDO $pdo;

    private int $run_id;

    private int $pmid;

    private string $state;

    private ?string $metadata;

    private array $data;

    public function __construct(\PDO $pdo, int $run_id, int $pmid, string $state, string $metadata = null, array $data = [])
    {
        $this->pdo = $pdo;
        $this->run_id = $run_id;
        $this->pmid = $pmid;
        $this->state = $state;
        $this->metadata = $metadata;
        $this->data = $data;
    }

    public function data(): array
    {
        $data = [
            'pmid' => $this->pmid,
            'state' => $this->state,
            'pending' => $this->data['state'] === 'pending',
            'selected' => $this->data['state'] === 'selected',
            'discarded' => $this->data['state'] === 'discarded',
            'curated' => $this->data['state'] === 'curated',
            'metadata' => $this->metadata,
        ];

        return $data + $this->data + [
            'url' => [
                'run_id' => $this->run_id,
                'pmid' => $this->pmid,
            ],
        ];
    }

    public function descriptions(): DescriptionViewInterface
    {
        return new DescriptionViewSql($this->pdo, $this->run_id, $this->pmid);
    }
}
