<?php

declare(strict_types=1);

namespace App\ReadModel;

interface PeptideViewInterface
{
    const MIN_LENGTH = 5;
    const MAX_LENGTH = 20;

    public function all(int $run_id, int $pmid, int $id): Statement;
}
