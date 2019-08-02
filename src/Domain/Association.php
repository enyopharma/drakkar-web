<?php

declare(strict_types=1);

namespace Domain;

final class Association implements ResourceInterface
{
    const PENDING = 'pending';
    const SELECTED = 'selected';
    const DISCARDED = 'discarded';
    const CURATED = 'curated';

    private $run_id;

    private $pmid;

    public function __construct(int $run_id, int $pmid)
    {
        $this->run_id = $run_id;
        $this->pmid = $pmid;
    }

    public function id(): array
    {
        return [
            'run_id' => $this->run_id,
            'pmid' => $this->pmid,
        ];
    }
}
