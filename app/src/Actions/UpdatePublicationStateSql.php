<?php

declare(strict_types=1);

namespace App\Actions;

use App\Assertions\PublicationState;

final class UpdatePublicationStateSql implements UpdatePublicationStateInterface
{
    const UPDATE_PUBLICATION_SQL = <<<SQL
        UPDATE associations
        SET state = ?, annotation = ?, updated_at = NOW()
        WHERE run_id = ? AND pmid = ?
    SQL;

    public function __construct(
        private \PDO $pdo,
    ) {}

    public function update(int $run_id, int $pmid, string $state, string $annotation): UpdatePublicationStateResult
    {
        PublicationState::argument($state);

        $update_publication_sth = $this->pdo->prepare(self::UPDATE_PUBLICATION_SQL);

        if ($update_publication_sth === false) throw new \Exception;

        $update_publication_sth->execute([$state, $annotation, $run_id, $pmid]);

        return $update_publication_sth->rowCount() == 1
            ? UpdatePublicationStateResult::success()
            : UpdatePublicationStateResult::notFound();
    }
}
