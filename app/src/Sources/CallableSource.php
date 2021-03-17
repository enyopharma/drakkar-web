<?php

declare(strict_types=1);

namespace App\Sources;

/**
 * @implements \IteratorAggregate<mixed>
 */
final class CallableSource implements \IteratorAggregate
{
    private PHPFileSource $files;

    private array $xs;

    /**
     * @param \App\Sources\PHPFileSource    $files
     * @param mixed                         ...$xs
     */
    public function __construct(PHPFileSource $files, ...$xs)
    {
        $this->files = $files;
        $this->xs = $xs;
    }

    public function getIterator()
    {
        foreach ($this->files as $key => $f) {
            if (!is_callable($f)) {
                throw new \UnexpectedValueException(
                    sprintf('Value returned by file \'%s\' must be a callable value, %s returned', $key, gettype($f)),
                );
            }

            $value = $f(...$this->xs);

            if (!is_array($value)) {
                throw new \UnexpectedValueException(
                    sprintf('Value returned by the callable from file \'%s\' must be an array, %s returned', $key, gettype($value)),
                );
            }

            yield from $value;
        }
    }
}
