<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;

use App\Input\Description;

final class ValidateDescriptionMiddleware extends AbstractValidationMiddleware
{
    public function __construct(ResponseFactoryInterface $factory)
    {
        parent::__construct(Description::class, $factory);
    }
}
