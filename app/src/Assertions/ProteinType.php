<?php

declare(strict_types=1);

namespace App\Assertions;

final class ProteinType
{
    /**
     * @var string
     */
    public const H = 'h';

    /**
     * @var string
     */
    public const V = 'v';

    public static function isValid(string $type): bool
    {
        return in_array($type, [self::H, self::V]);
    }

    public static function argument(string $type): void
    {
        if (!self::isValid($type)) {
            throw new \InvalidArgumentException(
                vsprintf('\'%s\' is not a valid curation run type (\'%s\' or \'%s\').', [
                    $type,
                    self::H,
                    self::V,
                ])
            );
        }
    }
}
