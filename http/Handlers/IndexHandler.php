<?php declare(strict_types=1);

namespace Http\Handlers;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use App\Repositories\RunRepository;

final class IndexHandler implements RequestHandlerInterface
{
    private $repository;

    private $engine;

    private $factory;

    public function __construct(
        RunRepository $repository,
        Engine $engine,
        ResponseFactoryInterface $factory
    ) {
        $this->repository = $repository;
        $this->engine = $engine;
        $this->factory = $factory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $this->engine->render('index', [
            'runs' => $this->repository->all(),
        ]);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return $response;
    }
}
