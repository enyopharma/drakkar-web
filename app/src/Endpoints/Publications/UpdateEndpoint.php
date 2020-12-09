<?php

declare(strict_types=1);

namespace App\Endpoints\Publications;

use Psr\Http\Message\ResponseInterface;

use App\Actions\UpdatePublicationStateResult;
use App\Actions\UpdatePublicationStateInterface;
use App\Assertions\PublicationState;

final class UpdateEndpoint
{
    public function __construct(
        private UpdatePublicationStateInterface $action,
    ) {}

    public function __invoke(callable $input, callable $responder): ResponseInterface|false
    {
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $state = $input('state', '');
        $annotation = $input('annotation', '');
        $source = $input('_source', '');

        if (!PublicationState::isValid($state)) {
            return false;
        }

        $result = $this->action->update($run_id, $pmid, $state, $annotation);

        return match ($result->status()) {
            0 => $responder(302, $source),
            1 => false,
        };
    }
}
