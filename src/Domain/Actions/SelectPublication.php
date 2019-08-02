<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\PublicationData;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\PublicationViewInterface;

final class SelectPublication
{
    private $runs;

    private $publications;

    public function __construct(
        RunViewInterface $runs,
        PublicationViewInterface $publications
    ) {
        $this->runs = $runs;
        $this->publications = $publications;
    }

    public function __invoke(int $run_id, int $pmid): DomainPayloadInterface
    {
        $select_run_sth = $this->runs->id($run_id);

        if (! $run = $select_run_sth->fetch()) {
            return new ResourceNotFound('run', ['id' => $run_id]);
        }

        $select_publication_sth = $this->publications->pmid($run_id, $pmid);

        if (! $publication = $select_publication_sth->fetch()) {
            return new ResourceNotFound('publication', [
                'run_id' => $run_id,
                'pmid' => $pmid,
            ]);
        }

        return new PublicationData($run, $publication);
    }
}