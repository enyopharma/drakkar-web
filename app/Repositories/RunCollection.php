<?php declare(strict_types=1);

namespace App\Repositories;

final class RunCollection implements \IteratorAggregate
{
    private $stmt;

    private $nbs;

    public function __construct(\PDOStatement $stmt, array $nbs)
    {
        $this->stmt = $stmt;
        $this->nbs = $nbs;
    }

    public function getIterator()
    {
        while ($run = $this->stmt->fetch()) {
            yield $run + [
                'nbs' => array_map(function ($nb) use ($run) {
                    return $nb[$run['id']]['count'] ?? 0;
                }, $this->nbs),
            ];
        }
    }
}
