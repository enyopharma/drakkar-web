<?php declare(strict_types=1);

namespace App\Http\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\MethodProjection;
use App\ReadModel\NotFoundException;
use App\Http\Responders\JsonResponder;


final class ShowHandler implements RequestHandlerInterface
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
        $attributes = (array) $request->getAttributes();

        $psimi_id = $attributes['psimi_id'] ?? '';

        try {
            return $this->responder->response([
                'method' => $this->methods->psimi_id($psimi_id),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }
    }
}
