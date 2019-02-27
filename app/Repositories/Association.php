<?php declare(strict_types=1);

namespace App\Repositories;

final class Association
{
    const PENDING = 'pending';
    const SELECTED = 'selected';
    const DISCARDED = 'discarded';
    const CURATED = 'curated';

    const STATES = [
        self::PENDING,
        self::SELECTED,
        self::DISCARDED,
        self::CURATED,
    ];
}
