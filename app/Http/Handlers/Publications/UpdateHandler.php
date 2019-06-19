<?php declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\UpdatePublicationState;

use Enyo\Http\Responders\HtmlResponder;

final class UpdateHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(UpdatePublicationState $domain, HtmlResponder $responder)
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
        $state = $body['state'];
        $annotation = $body['annotation'];
        $redirect = $body['redirect'] ?? '';

        $payload = ($this->domain)($run_id, $pmid, $state, $annotation);

        return $payload->parsed($this->success($redirect), [
            UpdatePublicationState::NOT_FOUND => [$this->responder, 'notfound'],
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
