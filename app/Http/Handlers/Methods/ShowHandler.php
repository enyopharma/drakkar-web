<?php declare(strict_types=1);

namespace App\Http\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\PsimiInterface;
use App\Http\Responders\JsonResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $psimi;

    private $responder;

    public function __construct(PsimiInterface $psimi, JsonResponder $responder)
    {
        $this->psimi = $psimi;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $psimi_id = $attributes['psimi_id'];

        try {
            return $this->responder->response([
                'method' => $this->psimi->method($psimi_id)->data(),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }
    }
}
