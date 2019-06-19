<?php declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Publication;
use App\ReadModel\PublicationProjection;
use App\ReadModel\DescriptionProjection;

use Enyo\ReadModel\NotFoundException;
use Enyo\Http\Responders\HtmlResponder;

final class EditHandler implements RequestHandlerInterface
{
    private $publications;

    private $descriptions;

    private $responder;

    public function __construct(
        PublicationProjection $publications,
        DescriptionProjection $descriptions,
        HtmlResponder $responder
    ) {
        $this->publications = $publications;
        $this->descriptions = $descriptions;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $run_id = (int) $attributes['run_id'];
        $pmid = (int) $attributes['pmid'];
        $id = (int) $attributes['id'];

        try {
            return $this->responder->template('descriptions/create', [
                'publication' => $this->publications->pmid($run_id, $pmid),
                'description' => $this->descriptions->id($run_id, $pmid, $id),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }
    }
}
