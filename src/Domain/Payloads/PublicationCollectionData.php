<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class PublicationCollectionData extends DomainData
{
    public function __construct(array $run, array $publications, string $state, array $pagination)
    {
        parent::__construct($publications, [
            'run' => $run,
            'state' => $state,
            'pagination' => $pagination,
        ]);
    }
}
