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

        return $this->action->delete($run_id, $pmid, $id)->match([
            DeleteDescriptionResult::SUCCESS => fn () => [],
            DeleteDescriptionResult::NOT_FOUND => fn () => false,
        ]);
    }
}
