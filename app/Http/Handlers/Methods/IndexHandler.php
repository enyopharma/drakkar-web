<?php declare(strict_types=1);

namespace App\Http\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\MethodProjection;

use Enyo\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
{
    private $methods;

    private $responder;

    public function __construct(MethodProjection $methods, JsonResponder $responder)
    {
        $this->methods = $methods;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = (array) $request->getQueryParams();

        $q = $query['q'] ?? '';

        return $this->responder->response([
            'methods' => $this->methods->search($q),
        ]);
    }
}
