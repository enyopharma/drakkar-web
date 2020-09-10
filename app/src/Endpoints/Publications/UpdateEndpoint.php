<?php

declare(strict_types=1);

namespace App\Endpoints\Publications;

use App\Actions\UpdatePublicationStateResult;
use App\Actions\UpdatePublicationStateInterface;
use App\Assertions\PublicationState;

final class UpdateEndpoint
{
    private UpdatePublicationStateInterface $action;

    public function __construct(UpdatePublicationStateInterface $action)
    {
        $this->action = $action;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|false
     */
    public function __invoke(callable $input, callable $responder)
    {
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $state = $input('state', '');
        $annotation = $input('annotation', '');
        $source = $input('_source', '');

        if (!PublicationState::isValid($state)) {
            return false;
        }

        return $this->action->update($run_id, $pmid, $state, $annotation)->match([
            UpdatePublicationStateResult::SUCCESS => fn () => $responder(302, $source),
            UpdatePublicationStateResult::NOT_FOUND => fn () => false,
        ]);
    }
}
