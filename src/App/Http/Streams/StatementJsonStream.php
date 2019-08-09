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
        $str = '';

        if ($this->i == 0) $str = '[';

        if ($current = $this->statement->fetch()) {
            if ($this->i > 0) $str.= ',';

            $this->i++;

            return $str . "\n" . $this->formatted($current);
        }

        $this->eof = true;

        return $str == '[' ? '[]' : $str . "\n" . ']';
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

    private function formatted(array $current): string
    {
        $json = (string) json_encode($current, JSON_PRETTY_PRINT);

        return (string) preg_replace('/^.+?$/m', '    $0', $json);
    }
}
