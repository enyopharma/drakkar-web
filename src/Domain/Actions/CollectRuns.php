<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\RunCollection;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\RunViewInterface;

final class CollectRuns implements DomainActionInterface
{
    private $runs;

    public function __construct(RunViewInterface $runs)
    {
        $this->runs = $runs;
    }

    public function __invoke(array $input): DomainPayloadInterface
    {
        $runs = $this->runs->all()->fetchAll();

        return new RunCollection($runs);
    }
}
