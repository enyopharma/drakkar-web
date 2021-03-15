<?php

declare(strict_types=1);

namespace App\Routing;

use FastRoute\RouteParser;

final class UrlGenerator
{
    public function __construct(
        private RouteParser $parser,
        private array $map = [],
    ) {}

    public function register(string $name, string $pattern): void
    {
        $this->map[$name] = $pattern;
    }

    public function isDefined(string $name): bool
    {
        return array_key_exists($name, $this->map);
    }

    public function generate(string $name, array $placeholders = [], array $query = [], string $fragment = ''): string
    {
        $pattern = $this->map[$name] ?? null;

        if (is_null($pattern)) {
            throw new \LogicException(sprintf('route name \'%s\' not found', $name));
        }

        $signatures = $this->parser->parse($pattern);

        foreach ($signatures as $signature) {
            $names = array_map(fn ($x) => $x[0], array_filter($signature, 'is_array'));

            $isect = array_intersect(array_keys($placeholders), $names);

            if (count($names) == count($isect)) {
                return $this->path($signature, $placeholders)
                    . $this->query($query)
                    . $this->fragment($fragment);
            }
        }

        throw new \LogicException(sprintf('invalid placeholders for route \'%s\'', $name));
    }

    private function path(array $signature, array $placeholders): string
    {
        if (count($signature) == 0) {
            return '';
        }

        $head = array_shift($signature);

        if (!is_array($head)) {
            return $head . $this->path($signature, $placeholders);
        }

        $placeholder = $placeholders[$head[0]];

        if (preg_match('~^' . $head[1] . '$~', (string) $placeholder) !== 0) {
            return $placeholder . $this->path($signature, $placeholders);
        }

        throw new \LogicException('given placeholder does not match pattern');
    }

    private function query(array $query): string
    {
        return $query = (count($query) > 0)
            ? '?' . http_build_query($query)
            : '';
    }

    private function fragment(string $fragment): string
    {
        return $fragment == '' ? '' : '#' . $fragment;
    }
}
