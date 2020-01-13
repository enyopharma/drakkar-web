<?php

declare(strict_types=1);

namespace App\Http\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\PublicationViewInterface;

use App\Http\Responders\HtmlResponder;

final class SearchHandler implements RequestHandlerInterface
{
    private $responder;

    private $publications;

    public function __construct(HtmlResponder $responder, PublicationViewInterface $publications)
    {
        $this->responder = $responder;
        $this->publications = $publications;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // get input.
        $params = (array) $request->getQueryParams();

        $pmid = (int) $params['pmid'];

        $publications = $this->publications->search($pmid)->fetchAll();

        return $this->responder->success('publications/search', [
            'pmid' => (string) ($params['pmid'] ?? ''),
            'publications' => $publications,
        ]);
    }
}
