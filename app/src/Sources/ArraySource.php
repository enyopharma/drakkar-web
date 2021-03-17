<?php

declare(strict_types=1);

namespace App\Sources;

/**
 * @implements \IteratorAggregate<mixed>
 */
final class ArraySource implements \IteratorAggregate
{
    private PHPFileSource $files;

    /**
     * @param \App\Sources\PHPFileSource $files
     */
    public function __construct(PHPFileSource $files)
    {
        $this->files = $files;
    }

    public function getIterator()
    {
        foreach ($this->files as $file => $value) {
            if(!is_array($value)) {
                throw new \UnexpectedValueException(
                    sprintf('Value returned by file \'%s\' must be an array, %s returned', $file, gettype($value)),
                );
            }

            yield from $value;
        }
    }
}
