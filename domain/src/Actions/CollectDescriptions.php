<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\PageOutOfRange;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DescriptionCollection;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\PublicationViewInterface;
use Domain\ReadModel\DescriptionViewInterface;

final class CollectDescriptions implements DomainActionInterface
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
        $page = (int) ($input['page'] ?? 1);
        $limit = (int) ($input['limit'] ?? 20);

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

        $offset = ($page - 1) * $limit;
        $total = $this->descriptions->count($run_id, $pmid);

        if ($page < 1) {
            return new PageOutOfRange(1, $limit);
        }

        if ($offset > 0 && $offset > $total) {
            return new PageOutOfRange((int) ceil($total/$limit), $limit);
        }

        $descriptions = $this->descriptions
            ->all($run_id, $pmid, $limit, $offset)
            ->fetchAll();

        return new DescriptionCollection($run, $publication, $descriptions, [
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
        ]);
    }
}