<?php

declare(strict_types=1);

namespace App\ReadModel;

interface TaxonViewInterface
{
    public function id(int $id, string ...$with): Statement;
}
