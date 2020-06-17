<?php

declare(strict_types=1);

namespace App\Assertions;

final class PublicationState
{
    /**
     * @var string
     */
    public const PENDING = 'pending';

    /**
     * @var string
     */
    public const SELECTED = 'selected';

    /**
     * @var string
     */
    public const DISCARDED = 'discarded';

    /**
     * @var string
     */
    public const CURATED = 'curated';

    public static function isValid(string $state): bool
    {
        return in_array($state, [
            self::PENDING,
            self::SELECTED,
            self::DISCARDED,
            self::CURATED,
        ]);
    }

    public static function argument(string $state): void
    {
        if (!self::isValid($state)) {
            throw new \InvalidArgumentException(
                vsprintf('\'%s\' is not a valid publication state (\'%s\', \'%s\', \'%s\' or \'%s\').', [
                    $state,
                    self::PENDING,
                    self::SELECTED,
                    self::DISCARDED,
                    self::CURATED,
                ])
            );
        }
    }
}
