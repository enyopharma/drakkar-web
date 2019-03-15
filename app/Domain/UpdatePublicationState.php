<?php declare(strict_types=1);

namespace App\Domain;

final class UpdatePublicationState
{
    const NOT_FOUND = 0;

    const UPDATE_ASSOCIATION_STH = <<<SQL
        UPDATE associations
        SET state = ?, annotation = ?, updated_at = NOW()
        WHERE run_id = ? AND publication_id = ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(
        int $run_id,
        int $publication_id,
        string $state,
        string $annotation
    ): DomainPayloadInterface {
        $update_association_sth = $this->pdo->prepare(self::UPDATE_ASSOCIATION_STH);

        $update_association_sth->execute([$state, $annotation, $run_id, $publication_id]);

        if ($update_association_sth->rowCount() == 0) {
            return new DomainError(self::NOT_FOUND);
        }

        return new DomainSuccess;
    }
}