<?php declare(strict_types=1);

namespace App\Http\Handlers\Methods;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\PsimiInterface;
use App\Http\Responders\JsonResponder;

final class IndexHandler implements RequestHandlerInterface
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
        $query = (array) $request->getQueryParams();

        $q = $query['q'] ?? '';

        return $this->responder->response([
            'methods' => $this->psimi->methods($q, 5),
        ]);
    }
}
