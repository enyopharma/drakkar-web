<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responder;
use App\Repositories\Publication;
use App\Repositories\RunRepository;
use App\Repositories\NotFoundException;
use App\Repositories\PublicationRepository;

final class ShowHandler implements RequestHandlerInterface
{
    private $runs;

    private $publications;

    private $responder;

    public function __construct(
        RunRepository $runs,
        PublicationRepository $publications,
        Responder $responder
    ) {
        $this->runs = $runs;
        $this->publications = $publications;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();
        $query = (array) $request->getQueryParams();

        $id = (int) $attributes['id'];
        $state = $query['state'] ?? Publication::PENDING;
        $page = (int) ($query['page'] ?? 1);

        try {
            $run = $this->runs->find($id);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }

        $publications = $this->publications->fromRun($id, $state, $page);

        if ($publications->overflow()) {
            return $this->responder->redirect('runs.show', $run, ['state' => $state]);
        }

        return $this->responder->html('runs/show', [
            'state' => $state,
            'run' => $run,
            'publications' => $publications,
        ]);
    }
}
