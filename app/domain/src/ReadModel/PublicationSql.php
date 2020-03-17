<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class PublicationSql implements PublicationInterface
{
    private $pdo;

    private $run_id;

    private $pmid;

    private $state;

    private $metadata;

    private $data;

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
            \Domain\Publication::PENDING => $this->data['state'] === \Domain\Publication::PENDING,
            \Domain\Publication::SELECTED => $this->data['state'] === \Domain\Publication::SELECTED,
            \Domain\Publication::DISCARDED => $this->data['state'] === \Domain\Publication::DISCARDED,
            \Domain\Publication::CURATED => $this->data['state'] === \Domain\Publication::CURATED,
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
