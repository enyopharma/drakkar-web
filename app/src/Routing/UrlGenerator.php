<?php

declare(strict_types=1);

namespace App\Routing;

final class UrlGenerator
{
    private array $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function isDefined(string $name): bool
    {
        return array_key_exists($name, $this->map);
    }

    public function generate(string $name, array $data = [], array $query = [], string $fragment = ''): string
    {
        if (!array_key_exists($name, $this->map)) {
            throw new \UnexpectedValueException(
                sprintf('no url named \'%s\'', $name)
            );
        }

        $url = $this->map[$name]($data);

        if (count($query) > 0) {
            $url.= '?' . http_build_query($query, '', '&amp;');
        }

        if (strlen($fragment) > 0) {
            $url.= '#' . $fragment;
        }

        return $url;
    }
}
