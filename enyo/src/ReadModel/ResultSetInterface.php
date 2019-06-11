<?php declare(strict_types=1);

namespace Enyo\ReadModel;

interface ResultSetInterface extends \Countable, \IteratorAggregate, \JsonSerializable
{
    public function map(callable $cb): ResultSetInterface;
}
