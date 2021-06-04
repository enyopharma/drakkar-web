<?php

declare(strict_types=1);

namespace App\ReadModel;

interface ProteinNameViewInterface
{
    public function names(int $id): Statement;
}
