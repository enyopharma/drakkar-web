<?php

declare(strict_types=1);

namespace Domain\Payloads;

use Domain\ReadModel\Statement;

final class Dataset extends DomainData
{
    public function __construct(Statement $statement)
    {
        parent::__construct(['statement' => $statement]);
    }
}
