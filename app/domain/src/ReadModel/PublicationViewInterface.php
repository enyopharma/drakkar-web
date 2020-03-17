<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface PublicationViewInterface
{
    public function search(int $pmid): Statement;
}
