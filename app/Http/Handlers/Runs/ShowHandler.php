<?php declare(strict_types=1);

namespace App\Http\Handlers\Runs;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responder;
use App\Domain\SelectRun;
use App\Domain\Publication;

final class ShowHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectRun $domain, Responder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();
        $query = (array) $request->getQueryParams();

        $id = (int) $attributes['id'];
        $state = $query['state'] ?? Publication::PENDING;
        $page = (int) $query['page'];

        $payload = ($this->domain)($id, $state, $page);

        return $payload->parsed($this->bind('success', $state, $page), [
            SelectRun::NOT_FOUND => $this->bind('notfound', $id),
            SelectRun::INVALID_STATE => $this->bind('invalidState', $id),
            SelectRun::UNDERFLOW => $this->bind('underflow', $id, $state),
            SelectRun::OVERFLOW => $this->bind('overflow', $id, $state),
        ]);
    }

    private function bind(string $method, ...$xs)
    {
        return function ($data) use ($method, $xs) {
            return $this->{$method}(...array_merge($xs, [$data]));
        };
    }

    private function success(string $state, int $page, array $data): ResponseInterface
    {
        return $this->responder->html('runs/show', array_merge($data, [
            'state' => $state,
            'page' => $page,
        ]));
    }

    private function notfound(int $id): ResponseInterface
    {
        return $this->responder->notfound();
    }

    private function invalidState(int $id): ResponseInterface
    {
        return $this->responder->notfound();
    }

    private function underflow(int $id, string $state): ResponseInterface
    {
        return $this->responder->redirect('runs.show', ['id' => $id], [
            'state' => $state,
            'page' => 1,
        ]);
    }

    private function overflow(int $id, string $state, array $data): ResponseInterface
    {
        return $this->responder->redirect('runs.show', ['id' => $id], [
            'state' => $state,
            'page' => $data['max'],
        ]);
    }
}
