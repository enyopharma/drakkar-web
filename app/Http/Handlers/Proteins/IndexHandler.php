<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\SelectProteins;

use Enyo\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectProteins $domain, JsonResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = (array) $request->getQueryParams();

        $type = $query['type'] ?? '';
        $q = $query['q'] ?? '';

        return ($this->domain)($type, $q)->parsed([$this->responder, 'response']);
    }
}
