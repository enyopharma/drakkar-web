<?php

declare(strict_types=1);

namespace App\Actions;

interface UpdatePublicationStateInterface
{
    public function update(int $run_id, int $pmid, string $state, string $annotation): UpdatePublicationStateResult;
}
