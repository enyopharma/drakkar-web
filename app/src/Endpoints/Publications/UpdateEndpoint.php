<?php

declare(strict_types=1);

namespace App\Endpoints\Publications;

use Psr\Http\Message\ResponseInterface;

use App\Actions\UpdatePublicationStateInterface;
use App\Assertions\PublicationState;

#[\App\Attributes\Method('PUT')]
#[\App\Attributes\Pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}')]
#[\App\Attributes\Name('runs.publications.update')]
final class UpdateEndpoint
{
    public function __construct(
        private UpdatePublicationStateInterface $action,
    ) {
    }

    public function __invoke(callable $input, callable $responder): ResponseInterface|null
    {
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $state = $input('state', '');
        $annotation = $input('annotation', '');
        $source = $input('_source', '');

        if (!PublicationState::isValid($state)) {
            return null;
        }

        $result = $this->action->update($run_id, $pmid, $state, $annotation);

        return match ($result->status()) {
            0 => $responder(302)->withHeader('location', $source),
            1 => null,
        };
    }
}
