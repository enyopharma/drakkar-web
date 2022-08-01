<?php

declare(strict_types=1);

namespace App\Input;

use App\Input\Validation\VariadicFactory;
use App\Input\Validation\InvalidDataException;

/**
 * @implements \IteratorAggregate<\App\Input\Isoform>
 */
final class IsoformList implements \IteratorAggregate, \JsonSerializable
{
    /**
     * @param mixed[] $data
     */
    public static function from(array $data): self
    {
        $factory = VariadicFactory::class(self::class)->array([Isoform::class, 'from']);

        return $factory($data);
    }

    /**
     * @var \App\Input\Isoform[]
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
