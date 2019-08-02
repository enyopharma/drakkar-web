<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class DescriptionCollectionData extends DomainData
{
    public function __construct(array $run, array $publication, array $descriptions, array $pagination)
    {
        parent::__construct($descriptions, [
            'run' => $run,
            'publication' => $publication,
            'pagination' => $pagination,
        ]);
    }
}
