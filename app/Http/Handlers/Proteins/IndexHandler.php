<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responder;
use Enyo\Http\Contents\Json;
use App\Domain\SelectProteins;

final class IndexHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(SelectProteins $domain, Responder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = (array) $request->getQueryParams();

        $type = $query['type'] ?? '';
        $q = $query['q'] ?? '';

        return ($this->domain)($type, $q)->parsed(function (array $data) {
            return $this->responder->json(new Json($data));
        });
    }
}
