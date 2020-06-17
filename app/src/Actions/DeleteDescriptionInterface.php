<?php

declare(strict_types=1);

namespace App\Actions;

interface DeleteDescriptionInterface
{
    public function delete(int $run_id, int $pmid, int $id): DeleteDescriptionResult;
}
