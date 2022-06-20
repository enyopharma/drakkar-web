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

    public function __invoke(callable $input): array|null
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
            1 => null,
            2 => null,
        };
    }
}
