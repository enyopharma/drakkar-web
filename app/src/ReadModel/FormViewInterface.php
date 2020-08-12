<?php

declare(strict_types=1);

namespace App\ReadModel;

interface FormViewInterface
{
    public function id(int $run_id, int $pmid, int $id): Statement;
}
