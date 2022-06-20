<?php

declare(strict_types=1);

namespace App\Actions;

use App\Input\DescriptionInput;

interface StoreDescriptionInterface
{
    public function store(int $run_id, int $pmid, DescriptionInput $input): StoreDescriptionResult;
}
