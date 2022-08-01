<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;

use App\Input\PeptideInput;

final class ValidatePeptideMiddleware extends AbstractValidationMiddleware
{
    public function __construct(ResponseFactoryInterface $factory)
    {
        parent::__construct(PeptideInput::class, $factory);
    }
}
