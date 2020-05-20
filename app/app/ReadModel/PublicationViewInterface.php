<?php

declare(strict_types=1);

namespace App\ReadModel;

interface PublicationViewInterface
{
    public function search(int $pmid): Statement;
}
