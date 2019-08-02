<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

final class HtmlResponseFactory
{
    private $factory;

    private $engine;

    private $helper;

    public function __construct(ResponseFactoryInterface $factory, Engine $engine, UrlHelper $helper)
    {
        $this->factory = $factory;
        $this->engine = $engine;
        $this->helper = $helper;
    }

    public function template(int $code, string $template, array $data = []): ResponseInterface
    {
        $body = $this->engine->render($template, $data);

        $response = $this->factory->createResponse($code);

        $response->getBody()->write($body);

        return $response->withHeader('content-type', 'text/html');
    }

    public function redirect(int $code, string $url): ResponseInterface
    {
        return $this->factory
            ->createResponse($code)
            ->withHeader('location', $url);
    }

    public function route(int $code, string $name, ...$xs): ResponseInterface
    {
        $url = $this->helper->generate($name, ...$xs);

        return $this->redirect($code, $url);
    }

    public function notfound(string $message = ''): ResponseInterface
    {
        $tpl = <<<EOT
<!doctype html>
<html>
    <head>
        <title>Not found</title>
    </head>
    <body>
        <h1>Not found</h1>
        %s
    </body>
</html>
EOT;

        $body = sprintf($tpl, empty($message) ? '' : '<p>' . $message . '</p>');

        $response = $this->factory->createResponse(404);

        $response->getBody()->write($body);

        return $response->withHeader('content-type', 'text/html');
    }
}
