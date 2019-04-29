<?php declare(strict_types=1);

namespace App\Domain;

final class SelectProtein
{
    const NOT_FOUND = 0;

    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT accession, name, description
        FROM proteins
        WHERE accession = ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(string $accession): DomainPayloadInterface
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);

        $select_protein_sth->execute([$accession]);

        if ($protein = $select_protein_sth->fetch()) {
            return new DomainSuccess([
                'protein' => $protein,
            ]);
        }

        return new DomainPayload(self::NOT_FOUND);
    }
}
