<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\Dataset;
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
        $statement = $this->dataset->all();

        return new Dataset($statement);
    }
}
