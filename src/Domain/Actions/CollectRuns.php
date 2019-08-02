<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\RunCollectionData;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\RunViewInterface;

final class CollectRuns
{
    private $runs;

    public function __construct(RunViewInterface $runs)
    {
        $this->runs = $runs;
    }

    public function __invoke(): DomainPayloadInterface
    {
        $runs = $this->runs->all()->fetchAll();

        return new RunCollectionData($runs);
    }
}
