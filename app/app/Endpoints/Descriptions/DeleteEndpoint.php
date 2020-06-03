<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use Psr\Http\Message\ServerRequestInterface;

use App\Actions\DeleteDescriptionResult;
use App\Actions\DeleteDescriptionInterface;

final class DeleteEndpoint
{
    private DeleteDescriptionInterface $action;

    public function __construct(DeleteDescriptionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * @return array|false
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');
        $id = (int) $request->getAttribute('id');

        return $this->action->delete($run_id, $pmid, $id)->match([
            DeleteDescriptionResult::SUCCESS => fn () => [],
            DeleteDescriptionResult::NOT_FOUND => fn () => false,
        ]);
    }
}
