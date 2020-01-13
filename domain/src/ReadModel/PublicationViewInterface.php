<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface PublicationViewInterface
{
    public function count(int $run_id, string $state): int;

    public function pmid(int $run_id, int $pmid): Statement;

    public function all(int $run_id, string $state, int $limit, int $offset): Statement;

    public function search(int $pmid): Statement;
}
