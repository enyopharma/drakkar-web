<?php declare(strict_types=1);

namespace Enyo\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Enyo\Http\Session;
use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

final class HtmlResponder
{
    private $session;

    private $generator;

    private $engine;

    private $factory;

    const NOT_FOUND_TPL = <<<EOT
<!DOCTYPE html>
<html>
    <head>
        <title>Not found</title>
    </head>
    <body>
        <h1>Not found</h1>
    </body>
</html>
EOT;

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

    public function back(): ResponseInterface
    {
        return $this->redirect(
            $this->session->previous()
        );
    }

    public function route(string $name, ...$xs): ResponseInterface
    {
        return $this->redirect(
            ($this->generator)($name, ...$xs)
        );
    }

    public function redirect(string $url): ResponseInterface
    {
        return $this->factory
            ->createResponse(302)
            ->withHeader('location', $url);
    }

    public function notfound(): ResponseInterface
    {
        return $this->response(self::NOT_FOUND_TPL, 404, 'not found');
    }

    public function unprocessable(): ResponseInterface
    {
        return $this->back();
    }

    public function template(string $template, array $data = []): ResponseInterface
    {
        return $this->response(
            $this->engine->render($template, $data)
        );
    }

    public function response(string $contents, int $code = 200, string $reason = ''): ResponseInterface
    {
        $response = $this->factory->createResponse($code, $reason);

        $response->getBody()->write($contents);

        return $response->withHeader('content-type', 'text/html');
    }
}
