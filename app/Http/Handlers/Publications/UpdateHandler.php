<?php declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responder;
use App\Domain\UpdatePublicationState;

final class UpdateHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(UpdatePublicationState $domain, Responder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();
        $body = (array) $request->getParsedBody();

        $run_id = (int) $attributes['run_id'];
        $publication_id = (int) $attributes['id'];
        $state = $body['state'];
        $annotation = $body['annotation'];

        $payload = ($this->domain)($run_id, $publication_id, $state, $annotation);

        return $payload->parsed($this->bind('success'), [
            UpdatePublicationState::NOT_FOUND => $this->bind('notfound', $run_id, $publication_id)
        ]);
    }

    private function bind(string $method, ...$xs)
    {
        return function ($data) use ($method, $xs) {
            return $this->{$method}(...array_merge($xs, [$data]));
        };
    }

    private function success(): ResponseInterface
    {
        return $this->responder->back();
    }

    private function notfound(int $run_id, int $publication_id): ResponseInterface
    {
        return $this->responder->notfound();
    }
}
