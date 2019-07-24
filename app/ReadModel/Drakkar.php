<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\ReadModel\Views\RepositoryInterface;

final class Drakkar implements DrakkarInterface
{
    private $repo;

    public function __construct(RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function run(int $id): RunInterface
    {
        if ($run = $this->repo->runs()->id($id)) {
            return new Run($this->repo, $run);
        }

        throw new NotFoundException(
            sprintf('No run with id %s', $id)
        );
    }

    public function runs(): ResultSet
    {
        return new ResultSet(...$this->repo->runs()->all());
    }

    public function dataset(): \Generator
    {
        return $this->repo->dataset()->all();
    }
}
