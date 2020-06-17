<?php

declare(strict_types=1);

namespace App\Endpoints\Publications;

use Psr\Http\Message\ServerRequestInterface;

use App\Actions\UpdatePublicationStateResult;
use App\Actions\UpdatePublicationStateInterface;

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
    public function __invoke(ServerRequestInterface $request, callable $responder)
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        $params = (array) $request->getParsedBody();

        $state = (string) ($params['state'] ?? '');
        $annotation = (string) ($params['annotation'] ?? '');
        $source = (string) ($params['_source'] ?? '');

        return $this->action->update($run_id, $pmid, $state, $annotation)->match([
            UpdatePublicationStateResult::SUCCESS => fn () => $responder(302, $source),
            UpdatePublicationStateResult::NOT_FOUND => fn () => false,
            UpdatePublicationStateResult::NOT_VALID => fn () => false,
        ]);
    }
}
