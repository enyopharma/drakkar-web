<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class Publication extends DomainData
{
    public function __construct(array $run, array $publication)
    {
        parent::__construct($publication, [
            'run' => $run,
        ]);
    }
}
