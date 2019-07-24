<?php

declare(strict_types=1);

namespace App\ReadModel;

interface PublicationInterface extends ResultInterface
{
    public function description(int $id): Result;

    public function descriptions(int $page, int $limit): Pagination;
}
