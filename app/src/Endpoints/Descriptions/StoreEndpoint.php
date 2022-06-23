<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use Psr\Http\Message\ResponseInterface;

use App\Input\DescriptionInput;
use App\Actions\StoreDescriptionInterface;
use App\Middleware\ValidateDescriptionMiddleware;

#[\App\Attributes\Method('POST')]
#[\App\Attributes\Pattern('/runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions')]
#[\App\Attributes\Middleware(ValidateDescriptionMiddleware::class)]
final class StoreEndpoint
{
    public function __construct(
        private StoreDescriptionInterface $action,
    ) {
    }

    public function __invoke(callable $input, callable $responder): ResponseInterface|array|null
    {
        // get the description input.
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $description = $input(DescriptionInput::class);

        if (!$description instanceof DescriptionInput) {
            throw new \LogicException;
        }

        // store the description.
        $result = $this->action->store($run_id, $pmid, $description);

        return match ($result->status()) {
            0 => ['id' => $result->id()],
            1 => null,
            2 => null,
            3 => $this->conflict($responder(), ...$result->messages()),
            4 => $this->conflict($responder(), 'Description already exists'),
            5 => $this->conflict($responder(), 'Failed to insert the description'),
            6 => $this->conflict($responder(), 'Failed to insert a new version of the description'),
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
