<?php

declare(strict_types=1);

namespace App\Actions;

use App\Input\Description;

interface StoreDescriptionInterface
{
    public function store(int $run_id, int $pmid, Description $input): StoreDescriptionResult;
}
