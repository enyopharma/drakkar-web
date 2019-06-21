<?php declare(strict_types=1);

namespace App\ReadModel\Protein;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\NotFoundException;
use Enyo\ReadModel\ResultSetInterface;

final class DomainProjection
{
    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT id FROM proteins WHERE accession = ?
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

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(string $accession): ResultSetInterface
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);
        $select_domains_sth = $this->pdo->prepare(self::SELECT_DOMAINS_SQL);

        $select_protein_sth->execute([$accession]);

        if ($protein = $select_protein_sth->fetch()) {
            $select_domains_sth->execute([$protein['id']]);

            $domains = $select_domains_sth->fetchAll();

            return new ResultSet($domains);
        }

        throw new NotFoundException(
            sprintf('%s has no entry with accession \'%s\'', self::class, $accession)
        );
    }
}
