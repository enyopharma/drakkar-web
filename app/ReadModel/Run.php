<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\ReadModel\Views\RepositoryInterface;

final class Run implements RunInterface
{
    private $repo;

    private $data;

    public function __construct(RepositoryInterface $repo, array $data)
    {
        $this->repo = $repo;
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function publication(int $pmid): PublicationInterface
    {
        $run_id = $this->data['id'];

        $view = $this->repo->publications();

        if ($publication = $view->pmid($run_id, $pmid)) {
            return new Publication($this->repo, $publication);
        }

        throw new NotFoundException(
            sprintf('No publication with run_id %s and pmid %s', $run_id, $pmid)
        );
    }

    public function publications(string $state, int $page, int $limit): Pagination
    {
        $run_id = $this->data['id'];

        $offset = ($page - 1) * $limit;

        $view = $this->repo->publications();

        $total = $view->count($this->data['id'], $state);

        if ($page < 1 || ($offset > 0 && $offset > $total)) {
            throw new \OutOfRangeException;
        }

        $publications = $view->all($run_id, $state, $limit, $offset);

        return new Pagination(
            new ResultSet(...$publications),
            $total,
            $page,
            $limit
        );
    }
}
