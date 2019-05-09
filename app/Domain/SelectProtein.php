<?php declare(strict_types=1);

namespace App\Domain;

final class SelectProtein
{
    const NOT_FOUND = 0;

    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT p.*, s.sequence
        FROM proteins AS p, sequences AS s
        WHERE p.id = s.protein_id
        AND s.is_canonical IS TRUE
        AND p.accession = ?
SQL;

    const SELECT_MATURES_SQL = <<<SQL
        SELECT name, start, stop
        FROM interactors
        WHERE protein_id = ?
        GROUP BY name, start, stop
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(string $accession): DomainPayloadInterface
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);
        $select_matures_sth = $this->pdo->prepare(self::SELECT_MATURES_SQL);

        $select_protein_sth->execute([$accession]);

        if ($protein = $select_protein_sth->fetch()) {
            $select_matures_sth->execute([$protein['id']]);

            $protein['matures'] = $select_matures_sth->fetchall();

            return new DomainSuccess([
                'protein' => $protein,
            ]);
        }

        return new DomainPayload(self::NOT_FOUND);
    }
}
