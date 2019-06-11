<?php declare(strict_types=1);

namespace App\ReadModel;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\NotFoundException;
use Enyo\ReadModel\ResultSetInterface;

final class ProteinProjection
{
    const SELECT_FROM_ID_SQL = <<<SQL
        SELECT p.id, p.type, p.accession, p.name, p.description, s.sequence
        FROM proteins AS p, sequences AS s
        WHERE p.id = s.protein_id AND s.is_canonical IS TRUE
        AND p.id = ?
SQL;

    const SELECT_FROM_ACCESSION_SQL = <<<SQL
        SELECT p.id, p.type, p.accession, p.name, p.description, s.sequence
        FROM proteins AS p, sequences AS s
        WHERE p.id = s.protein_id AND s.is_canonical IS TRUE
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

    public function id(int $id): array
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_FROM_ID_SQL);

        $select_protein_sth->execute([$id]);

        if ($protein = $select_protein_sth->fetch()) {
            return $this->formatted($protein);
        }

        throw new NotFoundException(
            sprintf('%s has no entry with id %s', self::class, $id)
        );
    }

    public function accession(string $accession): array
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_FROM_ACCESSION_SQL);

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
        $select_matures_sth = $this->pdo->prepare(self::SELECT_MATURES_SQL);
        $select_chains_sth = $this->pdo->prepare(self::SELECT_CHAINS_SQL);
        $select_domains_sth = $this->pdo->prepare(self::SELECT_DOMAINS_SQL);

        $select_isoforms_sth->execute([$protein['id']]);
        $select_matures_sth->execute([$protein['id']]);
        $select_chains_sth->execute([$protein['id']]);
        $select_domains_sth->execute([$protein['id']]);

        return array_merge($protein, [
            'isoforms' => $select_isoforms_sth->fetchall(\PDO::FETCH_KEY_PAIR),
            'matures' => $select_matures_sth->fetchall(),
            'chains' => $select_chains_sth->fetchall(),
            'domains' => $select_domains_sth->fetchall()
        ]);
    }
}
