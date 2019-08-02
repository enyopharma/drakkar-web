<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\PageOutOfRange;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;
use Domain\Payloads\PublicationCollectionData;
use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\PublicationViewInterface;

final class CollectPublications
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

    public function __invoke(int $run_id, string $state, int $page, int $limit): DomainPayloadInterface
    {
        if (empty($state)) $state = \Domain\Association::PENDING;

        $select_run_sth = $this->runs->id($run_id);

        if (! $run = $select_run_sth->fetch()) {
            return new ResourceNotFound('run', ['id' => $run_id]);
        }

        $offset = ($page - 1) * $limit;
        $total = $this->publications->count($run['id'], $state);

        if ($page < 1) {
            return new PageOutOfRange(1, $limit);
        }

        if ($offset > 0 && $offset > $total) {
            return new PageOutOfRange((int) ceil($total/$limit), $limit);
        }

        $publications = $this->publications
            ->all($run_id, $state, $limit, $offset)
            ->fetchAll();

        return new PublicationCollectionData($run, $publications, $state, [
            'page' => $page,
            'total' => $total,
            'limit' => $limit,
        ]);
    }
}
