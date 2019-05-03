<?php declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\InsertDescription;

use Enyo\Http\Responders\JsonResponder;

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
        $attributes = (array) $request->getAttributes();
        $body = (array) $request->getParsedBody();

        $run_id = (int) $attributes['run_id'];
        $pmid = (int) $attributes['pmid'];
        $method = $body['method'] ?? [];
        $interactor1 = $body['interactor1'] ?? [];
        $interactor2 = $body['interactor2'] ?? [];

        $payload = ($this->domain)(
            $run_id,
            $pmid,
            $method,
            $interactor1,
            $interactor2
        );

        return $payload->parsed([$this->responder, 'response'], [
            InsertDescription::ASSOCIATION_NOT_FOUND => [$this->responder, 'notfound'],
            InsertDescription::METHOD_NOT_FOUND => $this->failure('method not found'),
            InsertDescription::PROTEIN_NOT_FOUND => $this->failure('protein not found'),
            InsertDescription::INTERACTOR_FORMAT_ERROR => $this->failure('interactor format error'),
            InsertDescription::INTERACTOR_TYPE_ERROR => $this->failure('interactor type error'),
            InsertDescription::INTERACTOR_NAME_ERROR => $this->failure('interactor name error'),
            InsertDescription::INTERACTOR_POS_ERROR => $this->failure('interactor pos error'),
            InsertDescription::INTERACTOR_MAPPING_ERROR => $this->failure('interactor mapping error'),
            InsertDescription::NOT_UNIQUE => $this->failure('description already exists'),
        ]);
    }

    private function failure(string $reason): callable
    {
        return function (array $data) use ($reason) {
            return $this->responder->unprocessable($reason, $data);
        };
    }
}
