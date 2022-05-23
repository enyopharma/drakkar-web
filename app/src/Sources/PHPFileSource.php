<?php

declare(strict_types=1);

namespace App\Sources;

/**
 * @implements \IteratorAggregate<mixed>
 */
final class PHPFileSource implements \IteratorAggregate
{
    private array $patterns;

    public function __construct(string ...$patterns)
    {
        $this->patterns = $patterns;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->patterns as $pattern) {
            $files = glob($pattern);

            if (is_array($files)) {
                foreach ($files as $file) {
                    yield realpath($file) => require $file;
                }
            }
        }
    }
}
