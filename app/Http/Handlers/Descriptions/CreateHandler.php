<?php declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Publication;
use App\ReadModel\NotFoundException;
use App\ReadModel\PublicationProjection;
use App\Http\Responders\HtmlResponder;

final class CreateHandler implements RequestHandlerInterface
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

        $run_id = (int) $attributes['run_id'];
        $pmid = (int) $attributes['pmid'];

        try {
            return $this->responder->template('descriptions/create', [
                'publication' => $this->publications->pmid($run_id, $pmid),
                'description' => [],
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }
    }
}
