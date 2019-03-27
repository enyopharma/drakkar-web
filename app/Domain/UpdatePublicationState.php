<?php declare(strict_types=1);

namespace App\Domain;

final class UpdatePublicationState
{
    const NOT_FOUND = 0;

    const UPDATE_ASSOCIATION_SQL = <<<SQL
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
        $update_association_sth = $this->pdo->prepare(self::UPDATE_ASSOCIATION_SQL);

        $update_association_sth->execute([$state, $annotation, $run_id, $pmid]);

        return $update_association_sth->rowCount() == 0
            ? new DomainPayload(self::NOT_FOUND)
            : new DomainSuccess;
    }
}
