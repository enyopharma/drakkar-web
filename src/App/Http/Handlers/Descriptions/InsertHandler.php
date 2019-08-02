<?php

declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\Actions\DescriptionInput;
use Domain\Actions\CreateDescription;

use App\Http\Responders\JsonResponder;

final class InsertHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(CreateDescription $domain, JsonResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $input = DescriptionInput::fromRequest($request);

        $payload = ($this->domain)($input);

        return ($this->responder)($request, $payload);
    }
}
