<?php declare(strict_types=1);

namespace Http\Handlers\Publications;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use App\Repositories\PublicationRepository;

final class UpdateHandler implements RequestHandlerInterface
{
    private $publications;

    private $engine;

    private $factory;

    public function __construct(
        PublicationRepository $publications,
        ResponseFactoryInterface $factory
    ) {
        $this->publications = $publications;
        $this->factory = $factory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write(json_encode($request->getParsedBody()));

        return $response;
    }
}
