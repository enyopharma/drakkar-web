<?php

declare(strict_types=1);

namespace Domain\Actions;

interface PopulatePublicationInterface
{
    public function populate(int $pmid): PopulatePublicationResult;
}
