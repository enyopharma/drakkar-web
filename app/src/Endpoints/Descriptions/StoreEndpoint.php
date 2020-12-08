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
        return $this->action->store($input)->match([
            StoreDescriptionResult::SUCCESS => fn ($description) => $description,
            StoreDescriptionResult::INCONSISTENT_DATA => function ($_, ...$messages) use ($responder) {
                return $responder(409, $this->conflict(...$messages));
            },
            StoreDescriptionResult::DESCRIPTION_ALREADY_EXISTS => function () use ($responder) {
                return $responder(409, $this->conflict('Description already exists'));
            },
            StoreDescriptionResult::FIRST_VERSION_FAILURE => function () use ($responder) {
                return $responder(409, $this->conflict('Failed to insert the description'));
            },
            StoreDescriptionResult::NEW_VERSION_FAILURE => function () use ($responder) {
                return $responder(409, $this->conflict('Failed to insert a new version of the description'));
            },
        ]);
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
