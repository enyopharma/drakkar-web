<?php

declare(strict_types=1);

namespace Domain\Actions;

final class UpdatePublicationStateSql implements UpdatePublicationStateInterface
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

    public function update(int $run_id, int $pmid, string $state, string $annotation): UpdatePublicationStateResult
    {
        if (! in_array($state, \Domain\Publication::STATES)) {
            return UpdatePublicationStateResult::notValid();
        }

        $update_publication_sth = $this->pdo->prepare(self::UPDATE_PUBLICATION_SQL);

        $update_publication_sth->execute([$state, $annotation, $run_id, $pmid]);

        return $update_publication_sth->rowCount() == 1
            ? UpdatePublicationStateResult::success()
            : UpdatePublicationStateResult::notFound();
    }
}