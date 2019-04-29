<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\SelectProtein;

use Enyo\Http\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectProtein $domain, JsonResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $accession = $attributes['accession'];

        return ($this->domain)($accession)->parsed([$this->responder, 'response'], [
            SelectProtein::NOT_FOUND => [$this->responder, 'notfound'],
        ]);
    }
}
