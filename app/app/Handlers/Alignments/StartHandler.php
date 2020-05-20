<?php

declare(strict_types=1);

namespace App\Handlers\Alignments;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Predis\Client;

use App\Responders\JsonResponder;

final class StartHandler implements RequestHandlerInterface
{
    private Client $client;

    private JsonResponder $responder;

    public function __construct(Client $client, JsonResponder $responder)
    {
        $this->client = $client;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getParsedBody();

        $id = (string) $params['id'];
        $query = (string) $params['query'];
        $sequences = array_filter((array) ($params['sequences'] ?? []), 'is_string');

        $payload = json_encode(['id' => $id, 'query' => $query, 'sequences' => $sequences]);

        if ($payload === false) {
            throw new \Exception;
        }

        $this->client->rpush('default', [$payload]);

        return $this->responder->success();
    }
}
