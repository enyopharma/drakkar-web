<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use App\Actions\DeleteDescriptionInterface;

#[\App\Attributes\Method('DELETE')]
#[\App\Attributes\Pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}')]
final class DeleteEndpoint
{
    public function __construct(
        private DeleteDescriptionInterface $action,
    ) {
    }

    public function __invoke(callable $input): array|null
    {
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $id = (int) $input('id');

        $result = $this->action->delete($run_id, $pmid, $id);

        return match ($result->status()) {
            0 => [],
            1 => null,
        };
    }
}
