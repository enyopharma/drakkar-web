<?php

declare(strict_types=1);

namespace Domain\Actions;

final class DeleteDescriptionSql implements DeleteDescriptionInterface
{
    const DELETE_DESCRIPTION_SQL = <<<SQL
        UPDATE descriptions SET deleted_at = NOW() WHERE id = ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function delete(int $id): DeleteDescriptionResult
    {
        $delete_description_sth = $this->pdo->prepare(self::DELETE_DESCRIPTION_SQL);

        $delete_description_sth->execute([$id]);

        return $delete_description_sth->rowCount() == 1
            ? DeleteDescriptionResult::success()
            : DeleteDescriptionResult::notFound();
    }
}
