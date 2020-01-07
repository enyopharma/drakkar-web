<?php

declare(strict_types=1);

namespace App\Http\Streams;

use Psr\Http\Message\StreamInterface;

final class IteratorStream implements StreamInterface
{
    /**
     * @var int
     */
    private $i;

    /**
     * @var bool
     */
    private $eof;

    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @var callable
     */
    private $format;

    public static function json(\Traversable $traversable, int $options = 0, int $depth = 512): self
    {
        return new self($traversable, function ($i) use ($options, $depth) {
            return (string) json_encode($i, $options, $depth) . "\n";
        });
    }

    public function __construct(\Traversable $traversable, callable $format)
    {
        $this->i = 0;
        $this->eof = false;
        $this->iterator = new \IteratorIterator($traversable);
        $this->format = $format;
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function close()
    {
        throw new \RuntimeException;
    }

    public function detach()
    {
        return null;
    }

    public function getSize()
    {
        return null;
    }

    public function tell()
    {
        return $this->i;
    }

    public function eof()
    {
        return $this->eof;
    }

    public function isSeekable()
    {
        return false;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        throw new \RuntimeException;
    }

    public function rewind(): void
    {
        throw new \RuntimeException;
    }

    public function isWritable()
    {
        return false;
    }

    public function write($string)
    {
        throw new \RuntimeException;
    }

    public function isReadable()
    {
        return true;
    }

    public function read($length)
    {
        if ($this->i == 0) {
            $this->iterator->rewind();
        }

        if (! $this->eof) {
            $this->i++;

            $current = $this->iterator->current();

            $this->iterator->next();

            $this->eof = ! $this->iterator->valid();

            return ($this->format)($current);
        }

        throw new \RuntimeException;
    }

    public function getContents()
    {
        $contents = '';

        while (! $this->eof()) {
            $contents.= $this->read(8192);
        }

        return $contents;
    }

    public function getMetadata($key = null)
    {
        return is_null($key) ? [] : null;
    }
}
