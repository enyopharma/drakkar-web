<?php
namespace Psr\Http\Message;

namespace App\Http\Responders;

use Psr\Http\Message\StreamInterface;

final class IterableJsonStream implements StreamInterface
{
    private $i;

    private $eof;

    private $iterator;

    public function __construct(iterable $iterable)
    {
        $this->i = 0;
        $this->eof = false;
        $this->iterator = $this->iterableToIterator($iterable);

        $this->iterator->rewind();
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
        if ($this->iterator->valid()) {
            $prefix = $this->i == 0 ? '[' : ',';

            $current = $this->iterator->current();

            $this->i++;
            $this->iterator->next();

            return $prefix . "\n" . $this->formatted($current);
        }

        $this->eof = true;

        return "\n" . ']';
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

    private function iterableToIterator(iterable $iterable): \Iterator
    {
        if (is_array($iterable)) {
            return new \ArrayIterator($iterable);
        }

        if ($iterable instanceof \Iterator) {
            return $iterable;
        }

        return new \IteratorIterator($iterable);
    }

    private function formatted(array $current): string
    {
        $json = (string) json_encode($current, JSON_PRETTY_PRINT);

        return (string) preg_replace('/^.+?$/m', '    $0', $json);
    }
}
