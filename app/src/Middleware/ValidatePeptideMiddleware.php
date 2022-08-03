<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;

use App\Input\Peptide;

final class ValidatePeptideMiddleware extends AbstractValidationMiddleware
{
    public function __construct(ResponseFactoryInterface $factory)
    {
        parent::__construct(Peptide::class, $factory);
    }
}
