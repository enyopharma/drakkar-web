<?php

declare(strict_types=1);

namespace Domain\Actions;

use Quanta\Validation\ErrorInterface;

use Domain\Input\DescriptionInput;

interface StoreDescriptionInterface
{
    public function store(int $run_id, int $pmid, array $input): StoreDescriptionResult;
}
