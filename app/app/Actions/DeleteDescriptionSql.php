<?php

declare(strict_types=1);

namespace App\Actions;

final class DeleteDescriptionSql implements DeleteDescriptionInterface
{
    const SELECT_DESCRIPTION_SQL = <<<SQL
        SELECT d.id
        FROM associations AS a, descriptions AS d
        WHERE a.run_id = ?
        AND a.pmid = ?
        AND d.id = ?
    SQL;

    const DELETE_DESCRIPTION_SQL = <<<SQL
        UPDATE descriptions SET deleted_at = NOW() WHERE id = ?
    SQL;

    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function delete(int $run_id, int $pmid, int $id): DeleteDescriptionResult
    {
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);

        $select_description_sth->execute([$run_id, $pmid, $id]);

        if (!$select_description_sth->fetch()) {
            return DeleteDescriptionResult::notFound();
        }

        $delete_description_sth = $this->pdo->prepare(self::DELETE_DESCRIPTION_SQL);

        $delete_description_sth->execute([$id]);

        return $delete_description_sth->rowCount() == 1
            ? DeleteDescriptionResult::success()
            : DeleteDescriptionResult::notFound();
    }
}
