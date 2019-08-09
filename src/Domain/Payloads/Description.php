<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class Description extends DomainData
{
    public function __construct(array $run, array $publication, array $description)
    {
        parent::__construct($description, [
            'run' => $run,
            'publication' => $publication,
        ]);
    }
}
