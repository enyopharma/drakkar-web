<?php

declare(strict_types=1);

namespace Domain;

final class Publication
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
