<?php declare(strict_types=1);

namespace App\Domain;

final class DeleteDescription
{
    const NOT_FOUND = 0;

    const DELETE_DESCRIPTION_SQL = <<<SQL
        UPDATE descriptions SET deleted_at = NOW() WHERE id = ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(int $run_id, int $pmid, int $id): DomainPayloadInterface
    {
        $delete_description_sth = $this->pdo->prepare(self::DELETE_DESCRIPTION_SQL);

        $delete_description_sth->execute([$id]);

        return $delete_description_sth->rowCount() == 0
            ? new DomainPayload(self::NOT_FOUND)
            : new DomainSuccess;
    }
}
