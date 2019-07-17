<?php declare(strict_types=1);

namespace App\ReadModel;

final class Pagination implements ResultSetInterface
{
    private $rset;

    private $total;

    private $page;

    private $limit;

    public function __construct(ResultSetInterface $rset, int $total, int $page, int $limit)
    {
        $this->rset = $rset;
        $this->total = $total;
        $this->page = $page;
        $this->limit = $limit;
    }

    public function first(): array
    {
        return $this->rset->first();
    }

    public function count(): int
    {
        return $this->rset->count();
    }

    public function getIterator()
    {
        return $this->rset;
    }

    public function jsonSerialize()
    {
        return $this->rset->jsonSerialize();
    }

    public function page(): int
    {
        return $this->page;
    }

    public function total(): int
    {
        return (int) ceil($this->total/$this->limit);
    }

    public function overflow(): bool
    {
        $total = $this->total();

        return $total > 0 && $this->page > $total;
    }

    public function links(int $n = 10): array
    {
        // compute the total number of pages
        $total = $this->total();

        // show one block until $n pages
        if ($total <= $n) {
            return [$this->range(1, $total)];
        }

        // show two blocks when current page is close to the begining
        if ($this->page < $n - 2) {
            return [$this->range(1, 8), $this->range($total - 1, $total)];
        }

        // show two blocks when current page is close to the end
        if ($this->page > $total - $n + 3) {
            return [$this->range(1, 2), $this->range($total - 7, $total)];
        }

        // show three blocks when current page is not close to an edge
        return [
            $this->range(1, 2),
            $this->range($this->page - 3, $this->page + 3),
            $this->range($total - 1, $total)
        ];
    }

    private function link(int $page): array
    {
        return ['active' => $this->page == $page, 'page' => $page];
    }

    private function range(int $start, int $stop): array
    {
        return array_map([$this, 'link'], range($start, $stop));
    }
}
