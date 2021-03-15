<?php

declare(strict_types=1);

namespace App\Routing;

interface UrlPatternParserInterface
{
    public function parsed(string $pattern): ParsedUrlPattern;
}
