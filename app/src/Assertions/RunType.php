<?php

declare(strict_types=1);

namespace App\Assertions;

final class RunType
{
    /**
     * @var string
     */
    public const HH = 'hh';

    /**
     * @var string
     */
    public const VH = 'vh';

    public static function isValid(string $type): bool
    {
        return in_array($type, [self::HH, self::VH]);
    }

    public static function argument(string $type): void
    {
        if (!self::isValid($type)) {
            throw new \InvalidArgumentException(
                vsprintf('\'%s\' is not a valid curation run type (\'%s\' or \'%s\').', [
                    $type,
                    self::HH,
                    self::VH,
                ])
            );
        }
    }
}
