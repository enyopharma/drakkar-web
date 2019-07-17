<?php declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class JsonResponder
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function notfound(string $reason = 'not found'): ResponseInterface
    {
        return $this->response([], 404, $reason);
    }

    public function unprocessable(string $reason = 'invalid data', $data = []): ResponseInterface
    {
        return $this->response($data, 422, $reason);
    }

    public function response($data, int $code = 200, string $reason = ''): ResponseInterface
    {
        $response = $this->factory->createResponse($code, $reason);

        $response->getBody()->write(json_encode([
            'success' => $code == 200,
            'status' => $code,
            'reason' => $reason,
            'data' => $data,
        ]));

        return $response->withHeader('content-type', 'application/json');
    }
}