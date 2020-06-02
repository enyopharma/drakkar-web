<?php

declare(strict_types=1);

namespace App\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

final class PaginationExtension implements ExtensionInterface
{
    private Engine $engine;

    public function register(Engine $engine)
    {
        $this->engine = $engine;

        $engine->registerFunction('pagination', [$this, 'pagination']);
    }

    public function pagination(int $total, int $current, int $limit, callable $url, int $n = 10): string
    {
        $max = (int) ceil($total/$limit);

        return $this->engine->render('pagination/nav', [
            'prev' => ['active' => false, 'enabled' => $current > 1, 'page' => $current - 1],
            'links' => $this->links($max, $current, $n),
            'next' => ['active' => false, 'enabled' => $current < $max, 'page' => $current + 1],
            'url' => $url,
        ]);
    }

    private function links(int $max, int $current, int $n): array
    {
        // show one block until $n pages
        if ($max <= $n) {
            return [$this->range($current, 1, $max)];
        }

        // show two blocks when current page is close to the begining
        if ($current < $n - 2) {
            return [$this->range($current, 1, $n - 2), $this->range($current, $max - 1, $max)];
        }

        // show two blocks when current page is close to the end
        if ($current > $max - $n + 3) {
            return [$this->range($current, 1, 2), $this->range($current, $max - $n + 3, $max)];
        }

        // show three blocks when current page is not close to an edge
        return [
            $this->range($current, 1, 2),
            $this->range($current, $current - 3, $current + 3),
            $this->range($current, $max - 1, $max)
        ];
    }

    private function range(int $current, int $start, int $stop): array
    {
        return array_map(function ($page) use ($current) {
            return [
                'active' => $current == $page,
                'enabled' => true,
                'page' => $page,
            ];
        }, range($start, $stop));
    }
}
