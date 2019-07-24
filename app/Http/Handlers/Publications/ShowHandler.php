<?php declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\DrakkarInterface;
use App\ReadModel\NotFoundException;
use App\Http\Responders\HtmlResponder;

final class ShowHandler implements RequestHandlerInterface
{
    private $drakkar;

    private $responder;

    public function __construct(DrakkarInterface $drakkar, HtmlResponder $responder)
    {
        $this->drakkar = $drakkar;
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
            $run = $this->drakkar->run($run_id);
            $publication = $run->publication($pmid);
            $descriptions = $publication->descriptions($page, $limit);

            return $this->responder->template('publications/show', [
                'run' => $run->data(),
                'publication' => $publication->data(),
                'descriptions' => $descriptions,
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }

        catch (\OutOfRangeException $e) {
            return $this->responder->route('runs.publications.show', ['run_id' => $run_id, 'pmid' => $pmid], [
                'page' => 1,
            ]);
        }
    }
}
