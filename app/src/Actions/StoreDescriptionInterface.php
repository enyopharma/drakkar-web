<?php

declare(strict_types=1);

namespace App\Actions;

use Quanta\Validation\ErrorInterface;

use App\Input\DescriptionInput;

interface StoreDescriptionInterface
{
    public function store(DescriptionInput $input): StoreDescriptionResult;
}
