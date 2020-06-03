<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use Psr\Http\Message\ServerRequestInterface;

use App\Input\DescriptionInput;
use App\Actions\StoreDescriptionResult;
use App\Actions\StoreDescriptionInterface;

final class StoreEndpoint
{
    private StoreDescriptionInterface $action;

    public function __construct(StoreDescriptionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|array
     */
    public function __invoke(ServerRequestInterface $request, callable $responder)
    {
        // get the description input.
        $input = $request->getAttribute(DescriptionInput::class);

        if (!$input instanceof DescriptionInput) {
            throw new \LogicException;
        }

        // store the description.
        return $this->action->store($input)->match([
            StoreDescriptionResult::SUCCESS => fn ($description) => $description,
            StoreDescriptionResult::DESCRIPTION_ALREADY_EXISTS => function () use ($responder) {
                return $responder(409, $this->conflict('Description already exists'));
            },
            StoreDescriptionResult::STABLE_ID_FAILURE => function () use ($responder) {
                return $responder(409, $this->conflict('Failed to generate a stable id'));
            },
        ]);
    }

    private function conflict(string $reason): array
    {
        return [
            'code' => 409,
            'success' => false,
            'reason' => $reason,
            'data' => [],
        ];
    }
}
