<?php

declare(strict_types=1);

namespace Domain;

final class Publication implements ResourceInterface
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

    private $pmid;

    public function __construct(int $pmid)
    {
        $this->pmid = $pmid;
    }

    public function id(): array
    {
        return [
            'pmid' => $this->pmid,
        ];
    }
}
