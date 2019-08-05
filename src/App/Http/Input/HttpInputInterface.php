<?php

declare(strict_types=1);

namespace App\Http\Input;

use Psr\Http\Message\ServerRequestInterface;

interface HttpInputInterface
{
    public function __invoke(ServerRequestInterface $request): array;
}
