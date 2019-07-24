<?php

declare(strict_types=1);

namespace App\ReadModel;

use App\ReadModel\Views\RepositoryInterface;

final class Publication implements PublicationInterface
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

    public function description(int $id): Result
    {
        $run_id = $this->data['run_id'];
        $pmid = $this->data['pmid'];

        $view = $this->repo->descriptions();

        if ($description = $view->id($run_id, $pmid, $id)) {
            return new Result($description);
        }

        throw new NotFoundException(
            sprintf('No description with run_id %s, pmid %s and id %s', $run_id, $pmid, $id)
        );
    }

    public function descriptions(int $page, int $limit): Pagination
    {
        $run_id = $this->data['run_id'];
        $pmid = $this->data['pmid'];

        $offset = ($page - 1) * $limit;

        $view = $this->repo->descriptions();

        $total = $view->count($run_id, $pmid);

        if ($page < 1 || ($offset > 0 && $offset > $total)) {
            throw new \OutOfRangeException;
        }

        $descriptions = $view->all($run_id, $pmid, $limit, $offset);

        return new Pagination(
            new ResultSet(...$descriptions),
            $total,
            $page,
            $limit
        );
    }
}
