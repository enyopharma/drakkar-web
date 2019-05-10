<?php declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Enyo\Http\Responders\HtmlResponder;

final class CreateHandler implements RequestHandlerInterface
{
    private $responder;

    public function __construct(HtmlResponder $responder)
    {
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $run_id = (int) $attributes['run_id'];
        $pmid = (int) $attributes['pmid'];

        return $this->responder->template('descriptions/create', [
            'type' => 'vh',
            'description' => [
                'run_id' => $run_id,
                'pmid' => $pmid,
            ],
        ]);
    }
}
