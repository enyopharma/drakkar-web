<?php declare(strict_types=1);

namespace App\Http\Handlers\Proteins\Domains;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\Protein\DomainProjection;

use Enyo\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $domains;

    private $responder;

    public function __construct(DomainProjection $domains, JsonResponder $responder)
    {
        $this->domains = $domains;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $accession = $attributes['accession'] ?? '';

        return $this->responder->response([
            'domains' => $this->domains->all($accession),
        ]);
    }
}
