<?php declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Enyo\Http\Session;
use App\Repositories\PublicationRepository;

final class UpdateHandler implements RequestHandlerInterface
{
    private $session;

    private $publications;

    private $factory;

    public function __construct(
        Session $session,
        PublicationRepository $publications,
        ResponseFactoryInterface $factory
    ) {
        $this->session = $session;
        $this->publications = $publications;
        $this->factory = $factory;
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

        return $this->factory
            ->createResponse(302)
            ->withHeader('location', $this->session->previous());
    }
}