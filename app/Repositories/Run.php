<?php declare(strict_types=1);

namespace App\Repositories;

final class Run
{
    const HH = 'hh';
    const VH = 'vh';

    const PENDING = 'pending';
    const POPULATED = 'populated';

    const TYPES = [
        self::HH,
        self::VH,
    ];

    const STATES = [
        self::PENDING,
        self::POPULATED,
    ];
}
