<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\DomainData;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\DatasetViewInterface;

final class DownloadDataset implements DomainActionInterface
{
    private $dataset;

    public function __construct(DatasetViewInterface $dataset)
    {
        $this->dataset = $dataset;
    }

    public function __invoke(array $input): DomainPayloadInterface
    {
        $type = $input['type'];

        $dataset = $this->dataset->all($type);

        return new DomainData([
            'type' => $type,
            'dataset' => $dataset,
        ]);
    }
}
