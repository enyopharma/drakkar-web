<?php

declare(strict_types=1);

namespace App\Routing;

use FastRoute\RouteParser;

final class UrlGenerator
{
    private UrlPatternParserInterface $parser;

    private array $map;

    public function __construct(UrlPatternParserInterface $parser, array $map = [])
    {
        $this->parser = $parser;
        $this->map = $map;
    }

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

        $result = $this->parser->parsed($pattern)->path($placeholders);

        if ($result->isSuccess()) {
            return $result->path() . $this->query($query) . $this->fragment($fragment);
        }

        if ($result->isNoVariantMatching()) {
            throw new \LogicException($result->error($name));
        }

        if ($result->isPlaceholderFormatError()) {
            throw new \LogicException($result->error($name));
        }

        throw new \LogicException(sprintf('Unable to generate url for route named \'%s\'', $name));
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
