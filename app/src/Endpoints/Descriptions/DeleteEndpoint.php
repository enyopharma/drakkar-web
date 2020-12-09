<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use App\Actions\DeleteDescriptionResult;
use App\Actions\DeleteDescriptionInterface;

final class DeleteEndpoint
{
    public function __construct(
        private DeleteDescriptionInterface $action,
    ) {}

    public function __invoke(callable $input): array|false
    {
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $id = (int) $input('id');

        $result = $this->action->delete($run_id, $pmid, $id);

        return match ($result->status()) {
            0 => [],
            1 => false,
        };
    }
}
