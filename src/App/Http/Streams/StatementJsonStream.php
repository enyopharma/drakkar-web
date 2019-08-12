<?php

declare(strict_types=1);

namespace App\Http\Streams;

use Psr\Http\Message\StreamInterface;

use Domain\ReadModel\Statement;

final class StatementJsonStream implements StreamInterface
{
    private $i;

    private $eof;

    private $statement;

    public function __construct(Statement $statement)
    {
        $this->i = 0;
        $this->eof = false;
        $this->statement = $statement;
    }

    public function __toString()
    {
        return $this->getContents();
    }

    public function close()
    {
        //
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

    public function seek($offset, $whence = SEEK_SET)
    {
        throw new \RuntimeException;
    }

    public function rewind()
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
        if ($current = $this->statement->fetch()) {
            $this->i++;

            return (string) json_encode($current) . "\n";
        }

        $this->eof = true;

        return '';
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
