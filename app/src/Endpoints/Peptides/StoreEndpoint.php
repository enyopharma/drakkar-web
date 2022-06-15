<?php

declare(strict_types=1);

namespace App\Endpoints\Peptides;

use Psr\Http\Message\ResponseInterface;

use App\Input\PeptideInput;
use App\Actions\StorePeptideInterface;

final class StoreEndpoint
{
    public function __construct(private StorePeptideInterface $action)
    {
    }

    public function __invoke(callable $input, callable $responder): ResponseInterface|array|null
    {
        // get the description input.
        $run_id = (int) $input('run_id');
        $pmid = (int) $input('pmid');
        $id = (int) $input('id');
        $peptide = $input(PeptideInput::class);

        if (!$peptide instanceof PeptideInput) {
            throw new \LogicException;
        }

        // store the peptide.
        $result = $this->action->store($run_id, $pmid, $id, $peptide);

        return match ($result->status()) {
            0 => [],
            1 => $this->conflict($responder(), ...$result->messages()),
            2 => $this->conflict($responder(), ...$result->messages()),
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
