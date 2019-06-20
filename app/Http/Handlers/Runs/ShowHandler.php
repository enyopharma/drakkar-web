<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Publication;
use App\ReadModel\RunProjection;
use App\ReadModel\PublicationProjection;

use Enyo\ReadModel\NotFoundException;
use Enyo\ReadModel\OverflowException;
use Enyo\ReadModel\UnderflowException;
use Enyo\Http\Responders\HtmlResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $runs;

    private $publications;

    private $responder;

    public function __construct(
        RunProjection $runs,
        PublicationProjection $publications,
        HtmlResponder $responder
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
        $state = $query['state'] ?? \App\Domain\Publication::PENDING;
        $page = (int) ($query['page'] ?? 1);
        $limit = (int) ($query['limit'] ?? 20);

        if (! in_array($state, Publication::STATES)) {
            return $this->responder->notfound();
        }

        try {
            return $this->responder->template('runs/show', [
                'state' => $state,
                'run' => $this->runs->id($id),
                'publications' => $this->publications->pagination($id, $state, $page, $limit),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }

        catch (UnderflowException $e) {
            return $this->responder->route('runs.show', ['id' => $id], [
                'state' => $state,
                'page' => 1,
            ]);
        }

        catch (OverflowException $e) {
            return $this->responder->route('runs.show', ['id' => $id], [
                'state' => $state,
                'page' => $this->publications->maxPage($id, $state, $limit),
            ]);
        }
    }
}
