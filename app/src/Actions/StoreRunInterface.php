<?php

declare(strict_types=1);

namespace App\Actions;

interface StoreRunInterface
{
    public function store(string $type, string $name, int ...$pmids): StoreRunResult;
}
