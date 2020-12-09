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
            1 => $responder(409, $this->conflict(...$result->messages())),
            2 => $responder(409, $this->conflict('Description already exists')),
            3 => $responder(409, $this->conflict('Failed to insert the description')),
            4 => $responder(409, $this->conflict('Failed to insert a new version of the description')),
        };
    }

    private function conflict(string ...$messages): array
    {
        return [
            'code' => 409,
            'success' => false,
            'reason' => $messages,
            'data' => [],
        ];
    }
}
