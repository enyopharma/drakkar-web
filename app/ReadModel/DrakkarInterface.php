<?php

declare(strict_types=1);

namespace App\ReadModel;

interface DrakkarInterface
{
    public function run(int $id): RunInterface;

    public function runs(): ResultSet;

    public function dataset(): \Generator;
}
