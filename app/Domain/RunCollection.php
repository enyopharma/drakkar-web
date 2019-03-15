<?php declare(strict_types=1);

namespace App\Domain;

final class RunCollection implements \IteratorAggregate
{
    private $runs;

    private $nbs;

    public function __construct(array $runs, array $nbs)
    {
        $this->runs = $runs;
        $this->nbs = $nbs;
    }

    public function getIterator()
    {
        foreach ($this->runs as $run) {
            yield $run + [
                'nbs' => array_map(function ($nb) use ($run) {
                    return $nb[$run['id']]['count'] ?? 0;
                }, $this->nbs),
            ];
        }
    }
}
