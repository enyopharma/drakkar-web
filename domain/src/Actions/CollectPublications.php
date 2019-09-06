<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Payloads\PageOutOfRange;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainDataCollection;
use Domain\Payloads\DomainPayloadInterface;
use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\PublicationViewInterface;

final class CollectPublications implements DomainActionInterface
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

    public function __invoke(array $input): DomainPayloadInterface
    {
        $run_id = (int) $input['run_id'];
        $state = (string) ($input['state'] ?? \Domain\Association::PENDING);
        $page = (int) ($input['page'] ?? 1);
        $limit = (int) ($input['limit'] ?? 20);

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

        return new DomainDataCollection($publications, [
            'run' => $run,
            'page' => $page,
            'state' => $state,
            'pagination' => [
                'page' => $page,
                'total' => $total,
                'limit' => $limit,
            ],
        ]);
    }
}
