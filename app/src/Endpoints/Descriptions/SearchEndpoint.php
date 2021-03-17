<?php

declare(strict_types=1);

namespace App\Endpoints\Descriptions;

use Psr\Http\Message\ResponseInterface;

use League\Plates\Engine;

use Quanta\Http\UrlGenerator;

use App\ReadModel\DescriptionViewInterface;

final class SearchEndpoint
{
    public function __construct(
        private UrlGenerator $url,
        private Engine $engine,
        private DescriptionViewInterface $descriptions,
    ) {}

    public function __invoke(callable $input, callable $responder): ResponseInterface|string
    {
        $stable_id = trim($input('stable_id', ''));

        $description = $this->descriptions->search($stable_id)->fetch();

        if (!$description) {
            return $this->engine->render('descriptions/search', ['stable_id' => $stable_id]);
        }

        $url = $this->url->generate('runs.publications.descriptions.index', $description, ['stable_id' => $stable_id], 'descriptions');

        return $responder(302)->withHeader('location', $url);
    }
}
