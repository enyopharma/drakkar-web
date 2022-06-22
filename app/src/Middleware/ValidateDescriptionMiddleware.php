<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;

use App\Input\DescriptionInput;

final class ValidateDescriptionMiddleware extends AbstractValidationMiddleware
{
    public function __construct(ResponseFactoryInterface $factory)
    {
        parent::__construct(
            DescriptionInput::class,
            [DescriptionInput::class, 'fromRequest'],
            $factory,
        );
    }
}
