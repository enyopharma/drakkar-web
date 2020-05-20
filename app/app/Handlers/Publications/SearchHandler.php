<?php

declare(strict_types=1);

namespace App\Handlers\Publications;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\PublicationViewInterface;

use App\Responders\HtmlResponder;

final class SearchHandler implements RequestHandlerInterface
{
    private HtmlResponder $responder;

    private PublicationViewInterface $publications;

    public function __construct(HtmlResponder $responder, PublicationViewInterface $publications)
    {
        $this->responder = $responder;
        $this->publications = $publications;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = (array) $request->getQueryParams();

        $pmid = (int) $params['pmid'];

        $publications = $this->publications->search($pmid)->fetchAll();

        return $this->responder->success('publications/search', [
            'pmid' => (string) ($params['pmid'] ?? ''),
            'publications' => $publications,
        ]);
    }
}
