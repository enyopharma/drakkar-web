<?php

declare(strict_types=1);

namespace App\Input\Description;

use App\Input\Validation\VariadicInput;
use App\Input\Validation\VariadicFactory;
use App\Input\Validation\InvalidDataException;

/**
 * @implements \IteratorAggregate<\App\Input\Description\Isoform>
 */
final class IsoformList extends VariadicInput implements \IteratorAggregate, \JsonSerializable
{
    protected static function validation(VariadicFactory $factory): VariadicFactory
    {
        return $factory->array([Isoform::class, 'from']);
    }

    /**
     * @var \App\Input\Description\Isoform[]
     */
    public readonly array $isoforms;

    public function __construct(Isoform ...$isoforms)
    {
        if (count($isoforms) == 0) {
            throw InvalidDataException::error('%%s must not be empty');
        }

        $accessions = array_map(fn ($i) => $i->accession->value, $isoforms);

        if (count($accessions) > count(array_unique($accessions))) {
            throw InvalidDataException::error('%%s accessions must be unique');
        }

        $this->isoforms = $isoforms;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->isoforms);
    }

    public function jsonSerialize(): mixed
    {
        return $this->isoforms;
    }
}
