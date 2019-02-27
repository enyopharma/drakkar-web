<?php declare(strict_types=1);

namespace Http\Handlers\Runs;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use App\Repositories\Publication;
use App\Repositories\RunRepository;
use App\Repositories\PublicationRepository;

final class ShowHandler implements RequestHandlerInterface
{
    private $runs;

    private $publications;

    private $engine;

    private $factory;

    public function __construct(
        RunRepository $runs,
        PublicationRepository $publications,
        Engine $engine,
        ResponseFactoryInterface $factory
    ) {
        $this->runs = $runs;
        $this->publications = $publications;
        $this->engine = $engine;
        $this->factory = $factory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $input = array_merge($request->getAttributes(), $request->getQueryParams());

        $id = (int) $input['id'];
        $state = $input['state'] ?? Publication::PENDING;
        $page = $input['page'] ?? 1;
        $limit = $input['limit'] ?? 10;

        $body = $this->engine->render('runs/show', [
            'state' => $state,
            'run' => $this->runs->find((int) $input['id']),
            'publications' => $this->publications->fromRun((int) $input['id'], $state),
        ]);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return $response;
    }
}
