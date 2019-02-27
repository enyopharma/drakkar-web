<?php declare(strict_types=1);

namespace Http\Handlers\Publications;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Shared\Http\Session;
use App\Repositories\PublicationRepository;

final class UpdateHandler implements RequestHandlerInterface
{
    private $publications;

    private $engine;

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
        $input = array_merge($request->getAttributes(), $request->getParsedBody());

        $run_id = (int) $input['run_id'];
        $publication_id = (int) $input['id'];
        $state = $input['state'];
        $annotation = $input['annotation'];

        $this->publications->update($run_id, $publication_id, $state, $annotation);

        return $this->factory
            ->createResponse(302)
            ->withHeader('location', $this->session->previous());
    }
}
