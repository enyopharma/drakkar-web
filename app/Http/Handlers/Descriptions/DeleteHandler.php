<?php declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\DeleteDescription;

use Enyo\Http\Responders\HtmlResponder;

final class DeleteHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(DeleteDescription $domain, HtmlResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();
        $body = (array) $request->getParsedBody();

        $run_id = (int) $attributes['run_id'];
        $pmid = (int) $attributes['pmid'];
        $id = (int) $attributes['id'];
        $redirect = $body['redirect'] ?? '';

        $payload = ($this->domain)($run_id, $pmid, $id);

        return $payload->parsed($this->success($redirect), [
            DeleteDescription::NOT_FOUND => [$this->responder, 'notfound'],
        ]);
    }

    private function success(string $url): callable
    {
        return function () use ($url) {
            return $url == ''
                ? $this->responder->back()
                : $this->responder->redirect($url);
        };
    }
}