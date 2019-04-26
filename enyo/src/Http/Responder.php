<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use Zend\Expressive\Helper\UrlHelper;

final class Responder
{
    private $session;

    private $generator;

    private $engine;

    private $factory;

    public function __construct(
        Session $session,
        UrlHelper $generator,
        Engine $engine,
        ResponseFactoryInterface $factory
    ) {
        $this->session = $session;
        $this->generator = $generator;
        $this->engine = $engine;
        $this->factory = $factory;
    }

    public function notfound(): ResponseInterface
    {
        return $this->blank(404, 'Not found');
    }

    public function blank(int $code, string $reason): ResponseInterface
    {
        return $this->factory->createResponse($code, $reason);
    }

    public function back(): ResponseInterface
    {
        return $this->redirect($this->session->previous());
    }

    public function route(string $name, ...$xs): ResponseInterface
    {
        return $this->redirect(($this->generator)($name, ...$xs));
    }

    public function redirect(string $url): ResponseInterface
    {
        return $this->factory
            ->createResponse(302)
            ->withHeader('location', $url);
    }

    public function html(string $template, array $data = []): ResponseInterface
    {
        $contents = $this->engine->render($template, $data);

        $response = $this->factory->createResponse();

        $response->getBody()->write($contents);

        return $response->withHeader('content-type', 'text/html');
    }

    public function json($data, int $options = 0, int $depth = 512): ResponseInterface
    {
        $contents = json_encode($data, $options, $depth);

        $response = $this->factory->createResponse();

        $response->getBody()->write($contents);

        return $response->withHeader('content-type', 'application/json');
    }
}
