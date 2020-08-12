<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use Psr\Http\Message\ServerRequestInterface;

use League\Plates\Engine;

use App\Routing\UrlGenerator;
use App\ReadModel\DescriptionViewInterface;

final class SearchEndpoint
{
    private UrlGenerator $url;

    private Engine $engine;

    private DescriptionViewInterface $descriptions;

    public function __construct(UrlGenerator $url, Engine $engine, DescriptionViewInterface $descriptions)
    {
        $this->url = $url;
        $this->engine = $engine;
        $this->descriptions = $descriptions;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function __invoke(ServerRequestInterface $request, callable $responder)
    {
        $params = (array) $request->getQueryParams();

        $stable_id = trim($params['stable_id'] ?? '');

        $description = $this->descriptions->search($stable_id)->fetch();

        if (!$description) {
            return $this->engine->render('descriptions/search', ['stable_id' => $stable_id]);
        }

        $url = $this->url->generate('runs.publications.descriptions.index', $description, ['stable_id' => $stable_id]);

        return $responder(302, $url);
    }
}
