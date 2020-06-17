<?php

declare(strict_types=1);

namespace App\Endpoints\Publications;

use Psr\Http\Message\ServerRequestInterface;

use League\Plates\Engine;

use App\ReadModel\PublicationViewInterface;

final class SearchEndpoint
{
    private Engine $engine;

    private PublicationViewInterface $publications;

    public function __construct(Engine $engine, PublicationViewInterface $publications)
    {
        $this->engine = $engine;
        $this->publications = $publications;
    }

    /**
     * @return string
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $params = (array) $request->getQueryParams();

        $pmid = (int) $params['pmid'];

        $publications = $this->publications->search($pmid)->fetchAll();

        return $this->engine->render('publications/search', [
            'pmid' => (string) ($params['pmid'] ?? ''),
            'publications' => $publications,
        ]);
    }
}
