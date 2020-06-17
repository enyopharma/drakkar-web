<?php

declare(strict_types=1);

namespace App\Endpoints\Proteins;

use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\ProteinViewInterface;

final class ShowEndpoint
{
    private ProteinViewInterface $proteins;

    public function __construct(ProteinViewInterface $proteins)
    {
        $this->proteins = $proteins;
    }

    /**
     * @return array|false
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $accession = $request->getAttribute('accession');

        return $this->proteins
            ->accession($accession, 'isoforms', 'chains', 'domains', 'matures')
            ->fetch();
    }
}
