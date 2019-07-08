<?php declare(strict_types=1);

namespace App\ReadModel;

final class ProteinProjection
{
    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT p.id, p.type, p.accession, p.name, p.description, s.sequence
        FROM proteins AS p, sequences AS s
        WHERE p.id = s.protein_id AND s.is_canonical IS TRUE
        AND p.accession = ?
SQL;

    const SELECT_ISOFORMS_SQL = <<<SQL
        SELECT accession, sequence, is_canonical
        FROM sequences
        WHERE protein_id = ?
        ORDER BY is_canonical DESC, accession ASC
SQL;

    const SELECT_CHAINS_SQL = <<<SQL
        SELECT f.key, f.description, f.start, f.stop
        FROM sequences AS s, features AS f
        WHERE s.id = f.sequence_id
        AND s.is_canonical IS TRUE
        AND s.protein_id = ?
        AND f.key = 'CHAIN'
        ORDER BY start ASC, stop ASC
SQL;

    const SELECT_DOMAINS_SQL = <<<SQL
        SELECT f.key, f.description, f.start, f.stop
        FROM sequences AS s, features AS f
        WHERE s.id = f.sequence_id
        AND s.is_canonical IS TRUE
        AND s.protein_id = ?
        AND f.key IN ('TOPO_DOM', 'TRANSMEM', 'INTRAMEM', 'DOMAIN', 'REGION', 'MOTIF')
        ORDER BY start ASC, stop ASC
SQL;

    const SELECT_MATURES_SQL = <<<SQL
        SELECT i.name, i.start, i.stop
        FROM descriptions AS d, interactors AS i
        WHERE (i.id = d.interactor1_id OR i.id = d.interactor2_id)
        AND d.deleted_at IS NULL
        AND i.protein_id = ?
        GROUP BY i.name, i.start, i.stop
SQL;

    const SEARCH_PROTEINS_SQL = <<<SQL
        SELECT accession, name, description
        FROM proteins
        WHERE type = ? AND %s
        LIMIT ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function accession(string $accession): array
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);

        $select_protein_sth->execute([$accession]);

        if ($protein = $select_protein_sth->fetch()) {
            return $this->formatted($protein);
        }

        throw new NotFoundException(
            sprintf('%s has no entry with accession \'%s\'', self::class, $accession)
        );
    }

    public function search(string $type, string $q, int $limit = 20): ResultSetInterface
    {
        $parts = (array) preg_split('/\s+/', $q);

        $select_proteins_sth = $this->pdo->prepare(vsprintf(self::SEARCH_PROTEINS_SQL, [
            implode(' AND ', array_pad([], count($parts), 'search ILIKE ?')),
        ]));

        $select_proteins_sth->execute(array_merge([$type], array_map(function ($part) {
            return '%' . $part . '%';
        }, $parts), [$limit]));

        return new ResultSet($select_proteins_sth->fetchAll());
    }

    private function formatted(array $protein): array
    {
        $select_isoforms_sth = $this->pdo->prepare(self::SELECT_ISOFORMS_SQL);
        $select_chains_sth = $this->pdo->prepare(self::SELECT_CHAINS_SQL);
        $select_domains_sth = $this->pdo->prepare(self::SELECT_DOMAINS_SQL);
        $select_matures_sth = $this->pdo->prepare(self::SELECT_MATURES_SQL);

        $select_isoforms_sth->execute([$protein['id']]);
        $select_chains_sth->execute([$protein['id']]);
        $select_domains_sth->execute([$protein['id']]);
        $select_matures_sth->execute([$protein['id']]);

        return array_merge($protein, [
            'isoforms' => $select_isoforms_sth->fetchall(),
            'chains' => $select_chains_sth->fetchall(),
            'domains' => $select_domains_sth->fetchall(),
            'matures' => $select_matures_sth->fetchall(),
        ]);
    }
}
