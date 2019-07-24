<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Publication;
use App\ReadModel\DrakkarInterface;
use App\ReadModel\NotFoundException;
use App\Http\Responders\HtmlResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $drakkar;

    private $responder;

    public function __construct(DrakkarInterface $drakkar, HtmlResponder $responder)
    {
        $this->drakkar = $drakkar;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();
        $query = (array) $request->getQueryParams();

        $id = (int) $attributes['id'];
        $state = $query['state'] ?? \App\Domain\Publication::PENDING;
        $page = (int) ($query['page'] ?? 1);
        $limit = (int) ($query['limit'] ?? 20);

        if (! in_array($state, Publication::STATES)) {
            return $this->responder->notfound();
        }

        try {
            $run = $this->drakkar->run($id);
            $publications = $run->publications($state, $page, $limit);

            return $this->responder->template('runs/show', [
                'state' => $state,
                'run' => $run->data(),
                'publications' => $publications,
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }

        catch (\OutOfRangeException $e) {
            return $this->responder->route('runs.show', ['id' => $id], [
                'state' => $state,
                'page' => 1,
            ]);
        }
    }
}
