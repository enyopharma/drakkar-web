<?php

declare(strict_types=1);

namespace App\Http;

final class UrlGenerator
{
    private $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function generate(string $name, array $data = [], array $query = [], string $fragment = ''): string
    {
        $path = $this->map[$name]($data);

        $url = count($query) == 0 ? $path : $path . '?' . http_build_query($query);

        return strlen($fragment) == 0 ? $url : $url . '#' . $fragment;
    }
}
