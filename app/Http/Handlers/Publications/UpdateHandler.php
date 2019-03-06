<?php declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responder;
use App\Repositories\PublicationRepository;

final class UpdateHandler implements RequestHandlerInterface
{
    private $publications;

    private $responder;

    public function __construct(
        PublicationRepository $publications,
        Responder $responder
    ) {
        $this->publications = $publications;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();
        $body = (array) $request->getParsedBody();

        $run_id = (int) $attributes['run_id'];
        $publication_id = (int) $attributes['id'];
        $state = $body['state'];
        $annotation = $body['annotation'];

        $this->publications->update($run_id, $publication_id, $state, $annotation);

        return $this->responder->back();
    }
}
