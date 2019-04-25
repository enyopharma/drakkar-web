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

        return $payload->parsed($this->success($state, $page), [
            SelectRun::NOT_FOUND => [$this->responder, 'notfound'],
            SelectRun::INVALID_STATE => [$this->responder, 'notfound'],
            SelectRun::UNDERFLOW => $this->underflow($id, $state),
            SelectRun::OVERFLOW => $this->overflow($id, $state),
        ]);
    }

    private function success(string $state, int $page): callable
    {
        return function (array $data) use ($state, $page) {
            return $this->responder->html('runs/show', array_merge($data, [
                'state' => $state,
                'page' => $page,
            ]));
        };
    }

    private function underflow(int $id, string $state): callable
    {
        return function () use ($id, $state) {
            return $this->responder->redirect('runs.show', ['id' => $id], [
                'state' => $state,
                'page' => 1,
            ]);
        };
    }

    private function overflow(int $id, string $state): callable
    {
        return function (array $data) use ($id, $state) {
            return $this->responder->redirect('runs.show', ['id' => $id], [
                'state' => $state,
                'page' => $data['max'],
            ]);
        };
    }
}
