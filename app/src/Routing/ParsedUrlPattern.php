<?php

declare(strict_types=1);

namespace App\Routing;

final class ParsedUrlPattern
{
    private array $variants;

    public function __construct(UrlVariant $variant, UrlVariant ...$variants)
    {
        $this->variants = [$variant, ...$variants];
    }

    public function path(array $placeholders): MatchingResult
    {
        foreach ($this->variants as $variant) {
            $result = $variant->path($placeholders);

            if ($result->isSuccess() || $result->isPlaceholderFormatError()) {
                return $result;
            }
        }

        return MatchingResult::noVariantMatching();
    }
}
