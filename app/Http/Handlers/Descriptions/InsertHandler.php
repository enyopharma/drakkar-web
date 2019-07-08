<?php declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\DescriptionInput;
use App\Domain\InsertDescription;
use App\Http\Responders\JsonResponder;

final class InsertHandler implements RequestHandlerInterface
{
    private $domain;

    private $responder;

    public function __construct(InsertDescription $domain, JsonResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $input = DescriptionInput::fromRequest($request);

        $payload = ($this->domain)($input);

        return $payload->parsed([$this->responder, 'response'], [
            InsertDescription::NOT_FOUND => function (array $data) {
                return $this->responder->notfound($data['reason']);
            },

            InsertDescription::UNPROCESSABLE => function (array $data) {
                return $this->responder->unprocessable($data['reason']);
            },
        ]);
    }

    private function failure(string $reason): callable
    {
        return function (array $data) use ($reason) {
            return $this->responder->unprocessable($reason, $data);
        };
    }
}
