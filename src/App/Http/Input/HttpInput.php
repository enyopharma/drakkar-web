<?php

declare(strict_types=1);

namespace App\Http\Input;

use Psr\Http\Message\ServerRequestInterface;

final class HttpInput implements HttpInputInterface
{
    public function __invoke(ServerRequestInterface $request): array
    {
        return array_merge(
            (array) $request->getQueryParams(),
            (array) $request->getParsedBody(),
            (array) $request->getAttributes(),
            (array) $request->getUploadedFiles()
        );
    }
}
