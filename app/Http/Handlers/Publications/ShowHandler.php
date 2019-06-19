<?php declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\PublicationProjection;

use Enyo\Http\Responders\HtmlResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $publications;

    private $responder;

    public function __construct(PublicationProjection $publications, HtmlResponder $responder)
    {
        $this->publications = $publications;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();
        $body = (array) $request->getParsedBody();

        $run_id = (int) $attributes['run_id'];
        $pmid = (int) $attributes['pmid'];

        try {
            return $this->responder->template('publications/show', [
                'publication' => $this->publications->pmid($run_id, $pmid),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }
    }
}
