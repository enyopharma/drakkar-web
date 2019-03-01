<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

use App\Repositories\Publication;
use App\Repositories\RunRepository;
use App\Repositories\NotFoundException;
use App\Repositories\PublicationRepository;

final class ShowHandler implements RequestHandlerInterface
{
    private $runs;

    private $publications;

    private $url;

    private $engine;

    private $factory;

    public function __construct(
        RunRepository $runs,
        PublicationRepository $publications,
        UrlHelper $url,
        Engine $engine,
        ResponseFactoryInterface $factory
    ) {
        $this->runs = $runs;
        $this->publications = $publications;
        $this->url = $url;
        $this->engine = $engine;
        $this->factory = $factory;
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
            return $this->factory->createResponse(404, 'Not found');
        }

        $publications = $this->publications->fromRun($id, $state, $page);

        if ($publications->overflow()) {
            return $this->factory
                ->createResponse(302)
                ->withHeader('location', ($this->url)('runs.show', $run, ['state' => $state]));
        }

        $body = $this->engine->render('runs/show', [
            'state' => $state,
            'run' => $run,
            'publications' => $publications,
        ]);

        $response = $this->factory
            ->createResponse(200)
            ->withHeader('content-type', 'text/html');

        $response->getBody()->write($body);

        return $response;
    }
}
