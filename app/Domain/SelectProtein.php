<?php declare(strict_types=1);

namespace App\Domain;

final class SelectProtein
{
    const NOT_FOUND = 0;

    const DOMAINS = [
        'TOPO_DOM',
        'TRANSMEM',
        'INTRAMEM',
        'DOMAIN',
        'REGION',
        'MOTIF',
    ];

    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT p.*, s.sequence
        FROM proteins AS p, sequences AS s
        WHERE p.id = s.protein_id
        AND s.is_canonical IS TRUE
        AND p.accession = ?
SQL;

    const SELECT_ISOFORMS_SQL = <<<SQL
        SELECT accession, sequence
        FROM sequences
        WHERE protein_id = ?
        AND is_canonical IS FALSE
SQL;

    const SELECT_MATURES_SQL = <<<SQL
        SELECT name, start, stop
        FROM interactors
        WHERE protein_id = ?
        GROUP BY name, start, stop
SQL;

    const SELECT_FEATURES_SQL = <<<SQL
        SELECT f.key, f.description, f.start, f.stop
        FROM sequences AS s, features AS f
        WHERE s.id = f.sequence_id
        AND s.is_canonical IS TRUE
        AND s.protein_id = ?
        AND f.key IN ('TOPO_DOM', 'TRANSMEM', 'INTRAMEM', 'DOMAIN', 'REGION', 'MOTIF')
        ORDER BY start ASC, stop ASC
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(string $accession): DomainPayloadInterface
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);
        $select_isoforms_sth = $this->pdo->prepare(self::SELECT_ISOFORMS_SQL);
        $select_matures_sth = $this->pdo->prepare(self::SELECT_MATURES_SQL);
        $select_features_sth = $this->pdo->prepare(vsprintf(self::SELECT_FEATURES_SQL, [
            implode(', ', array_map(function () {
                return '\'' . $domain . '\'';
            }, self::DOMAINS))
        ]));

        $select_protein_sth->execute([$accession]);

        if ($protein = $select_protein_sth->fetch()) {
            $select_isoforms_sth->execute([$protein['id']]);
            $select_matures_sth->execute([$protein['id']]);
            $select_features_sth->execute([$protein['id']]);

            $protein['isoforms'] = $select_isoforms_sth->fetchall();
            $protein['matures'] = $select_matures_sth->fetchall();
            $protein['features'] = $select_features_sth->fetchall();

            return new DomainSuccess([
                'protein' => $protein,
            ]);
        }

        return new DomainPayload(self::NOT_FOUND);
    }
}
