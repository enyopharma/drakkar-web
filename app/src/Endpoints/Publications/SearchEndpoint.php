<?php

declare(strict_types=1);

namespace App\Endpoints\Publications;

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
    public function __invoke(callable $input)
    {
        $pmid = trim($input('pmid'));

        $publications = $this->publications->search((int) $pmid)->fetchAll();

        return $this->engine->render('publications/search', [
            'pmid' => $pmid,
            'publications' => $publications,
        ]);
    }
}
