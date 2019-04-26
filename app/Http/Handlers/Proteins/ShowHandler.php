<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responder;
use Enyo\Http\Contents\Json;
use App\Domain\SelectProtein;

final class ShowHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectProtein $domain, Responder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $id = (int) $attributes['id'];

        return ($this->domain)($id)->parsed($this->success(), [
            SelectProtein::NOT_FOUND => [$this->responder, 'notfound'],
        ]);
    }

    private function success(): callable
    {
        return function (array $data) {
            return $this->responder->json(new Json($data));
        };
    }
}
