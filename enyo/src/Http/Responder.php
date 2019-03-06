<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;

use Zend\Expressive\Helper\UrlHelper;

final class Responder
{
    private $session;

    private $url;

    private $engine;

    private $factory;

    public function __construct(
        Session $session,
        UrlHelper $url,
        Engine $engine,
        ResponseFactoryInterface $factory
    ) {
        $this->session = $session;
        $this->url = $url;
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

    public function redirect(string $urlOrName, ...$xs): ResponseInterface
    {
        $response = $this->factory->createResponse(302);

        return count($xs) > 0
            ? $response->withHeader('location', ($this->url)($urlOrName, ...$xs))
            : $response->withHeader('location', $urlOrName);
    }

    public function html(string $template, array $data): ResponseInterface
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
