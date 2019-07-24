<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\ReadModel\Views\RepositoryInterface;

final class Uniprot implements UniprotInterface
{
    private $repo;

    public function __construct(RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function protein(string $accession): Result
    {
        $view = $this->repo->proteins();

        if ($protein = $view->accession($accession)) {
            return new Result($protein);
        }

        throw new NotFoundException(
            sprintf('No protein with accession \'%s\'', $accession)
        );
    }

    public function proteins(string $type, string $q, int $limit): ResultSet
    {
        $view = $this->repo->proteins();

        $proteins = $view->search($type, $q, $limit);

        return new ResultSet(...$proteins);
    }
}
