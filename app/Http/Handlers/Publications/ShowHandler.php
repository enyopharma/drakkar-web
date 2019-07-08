<?php declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\NotFoundException;
use App\ReadModel\OverflowException;
use App\ReadModel\UnderflowException;
use App\ReadModel\PublicationProjection;
use App\ReadModel\DescriptionProjection;
use App\Http\Responders\HtmlResponder;

final class ShowHandler implements RequestHandlerInterface
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
        $query = (array) $request->getQueryParams();

        $run_id = (int) $attributes['run_id'];
        $pmid = (int) $attributes['pmid'];
        $page = (int) ($query['page'] ?? 1);
        $limit = (int) ($query['limit'] ?? 20);

        try {
            return $this->responder->template('publications/show', [
                'publication' => $this->publications->pmid($run_id, $pmid),
                'descriptions' => $this->descriptions->pagination($run_id, $pmid, $page, $limit),
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }

        catch (UnderflowException $e) {
            return $this->responder->route('runs.publications.show', ['run_id' => $run_id, 'pmid' => $pmid], [
                'page' => 1,
            ]);
        }

        catch (OverflowException $e) {
            return $this->responder->route('runs.publications.show', ['run_id' => $run_id, 'pmid' => $pmid], [
                'page' => $this->descriptions->maxPage($run_id, $pmid, $limit),
            ]);
        }
    }
}
