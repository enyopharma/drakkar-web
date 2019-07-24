<?php

declare(strict_types=1);

namespace App\ReadModel;

interface RunInterface extends ResultInterface
{
    public function publication(int $pmid): PublicationInterface;

    public function publications(string $state, int $page, int $limit): Pagination;
}
