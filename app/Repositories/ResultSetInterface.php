<?php declare(strict_types=1);

namespace App\Repositories;

interface ResultSetInterface extends \IteratorAggregate
{
    public function first(): array;

    public function count(): int;

    public function chunks(int $size = 10): \Generator;
}
