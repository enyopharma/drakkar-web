<?php

declare(strict_types=1);

namespace Cli\Actions;

interface PopulatePublicationInterface
{
    public function populate(int $pmid): PopulatePublicationResult;
}
