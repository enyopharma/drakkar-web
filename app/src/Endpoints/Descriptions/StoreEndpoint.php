<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use Psr\Http\Message\ResponseInterface;

use App\Input\DescriptionInput;
use App\Actions\StoreDescriptionResult;
use App\Actions\StoreDescriptionInterface;

final class StoreEndpoint
{
    public function __construct(
        private StoreDescriptionInterface $action,
    ) {}

    public function __invoke(callable $input, callable $responder): ResponseInterface|array
    {
        // get the description input.
        $input = $input(DescriptionInput::class);

        if (!$input instanceof DescriptionInput) {
            throw new \LogicException;
        }

        // store the description.
        $result = $this->action->store($input);

        return match ($result->status()) {
            0 => ['id' => $result->id()],
            1 => $this->conflict($responder(), ...$result->messages()),
            2 => $this->conflict($responder(), 'Description already exists'),
            3 => $this->conflict($responder(), 'Failed to insert the description'),
            4 => $this->conflict($responder(), 'Failed to insert a new version of the description'),
        };
    }

    private function conflict(ResponseInterface $response, string ...$messages): ResponseInterface
    {
        $contents = json_encode([
            'code' => 409,
            'success' => false,
            'reason' => $messages,
            'data' => [],
        ], JSON_THROW_ON_ERROR);

        $response->getBody()->write($contents);

        return $response
            ->withStatus(409)
            ->withHeader('content-type', 'application/json');
    }
}
