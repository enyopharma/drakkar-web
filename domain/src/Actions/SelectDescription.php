<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\DomainData;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\PublicationViewInterface;
use Domain\ReadModel\DescriptionViewInterface;

final class SelectDescription implements DomainActionInterface
{
    private $runs;

    private $publications;

    private $descriptions;

    public function __construct(
        RunViewInterface $runs,
        PublicationViewInterface $publications,
        DescriptionViewInterface $descriptions
    ) {
        $this->runs = $runs;
        $this->publications = $publications;
        $this->descriptions = $descriptions;
    }

    public function __invoke(array $input): DomainPayloadInterface
    {
        $run_id = (int) $input['run_id'];
        $pmid = (int) $input['pmid'];
        $id = (int) $input['id'];

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

        $select_description_sth = $this->descriptions->id($run_id, $pmid, $id);

        if (! $description = $select_description_sth->fetch()) {
            return new ResourceNotFound('description', [
                'run_id' => $run_id,
                'pmid' => $pmid,
                'id' => $id,
            ]);
        }

        return new DomainData($description, [
            'run' => $run,
            'publication' => $publication,
        ]);
    }
}
