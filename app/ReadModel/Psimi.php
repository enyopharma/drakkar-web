<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\ReadModel\Views\RepositoryInterface;

final class Psimi implements PsimiInterface
{
    private $repo;

    public function __construct(RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function method(string $psimi_id): Result
    {
        $view = $this->repo->methods();

        if ($method = $view->psimiId($psimi_id)) {
            return new Result($method);
        }

        throw new NotFoundException(
            sprintf('No method with psimi id \'%s\'', $psimi_id)
        );
    }

    public function methods(string $q, int $limit): ResultSet
    {
        $view = $this->repo->methods();

        $methods = $view->search($q, $limit);

        return new ResultSet(...$methods);
    }
}
