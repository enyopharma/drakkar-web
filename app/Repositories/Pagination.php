<?php declare(strict_types=1);

namespace App\Repositories;

final class Pagination implements ResultSetInterface
{
    private $rs;

    private $total;

    private $page;

    private $limit;

    public function __construct(ResultSetInterface $rs, int $total, int $page, int $limit)
    {
        $this->rs = $rs;
        $this->total = $total;
        $this->page = $page;
        $this->limit = $limit;
    }

    public function count(): int
    {
        return $this->rs->count();
    }

    public function chunks(int $size = 10): \Generator
    {
        return $this->rs->chunks($size);
    }

    public function getIterator()
    {
        return $this->rs;
    }

    public function pages(int $limit = 10): array
    {
        return [];
    }
}
