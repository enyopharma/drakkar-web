<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Association;
use Domain\Payloads\ResourceUpdated;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;

final class UpdatePublicationState
{
    const UPDATE_PUBLICATION_SQL = <<<SQL
        UPDATE associations
        SET state = ?, annotation = ?, updated_at = NOW()
        WHERE run_id = ? AND pmid = ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(int $run_id, int $pmid, string $state, string $annotation): DomainPayloadInterface
    {
        $update_publication_sth = $this->pdo->prepare(self::UPDATE_PUBLICATION_SQL);

        $update_publication_sth->execute([$state, $annotation, $run_id, $pmid]);

        return $update_publication_sth->rowCount() == 1
            ? new ResourceUpdated(new Association($run_id, $pmid))
            : new ResourceNotFound('publication', [
                'run_id' => $run_id,
                'pmid' => $pmid,
            ]);
    }
}
