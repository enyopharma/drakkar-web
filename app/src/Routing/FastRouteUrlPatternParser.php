<?php

declare(strict_types=1);

namespace App\Routing;

use FastRoute\RouteParser\Std;

final class FastRouteUrlPatternParser implements UrlPatternParserInterface
{
    private Std $parser;

    public static function default(): self
    {
        return new self(new Std);
    }

    public function __construct(Std $parser)
    {
        $this->parser = $parser;
    }

    public function parsed(string $pattern): ParsedUrlPattern
    {
        $variants = [];

        $parsed = $this->parser->parse($pattern);

        foreach ($parsed as $parts) {
            try {
                $variants[] = array_reduce($parts, [$this, 'reduced'], UrlVariant::empty());
            }

            catch (\Exception $e) {
                throw new \Exception(sprintf('Unable to parse url pattern \'%s\'', $pattern));
            }
        }

        return new ParsedUrlPattern(...$variants);
    }

    /**
     * @param mixed $part
     */
    private function reduced(UrlVariant $variant, $part): UrlVariant
    {
        if (is_string($part)) {
            return $variant->withConstant($part);
        }

        if (is_array($part) && count($part) == 2) {
            return $variant->withPlaceholder(...$part);
        }

        throw new \Exception;
    }
}
