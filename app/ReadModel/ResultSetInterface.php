<?php declare(strict_types=1);

namespace App\ReadModel;

interface ResultSetInterface extends \Countable, \IteratorAggregate, \JsonSerializable
{
    public function first(): array;
}
