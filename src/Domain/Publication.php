<?php

declare(strict_types=1);

namespace Domain;

final class Publication implements ResourceInterface
{
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
