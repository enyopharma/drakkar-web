<?php declare(strict_types=1);

namespace App\ReadModel\Protein;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\NotFoundException;
use Enyo\ReadModel\ResultSetInterface;

final class ChainProjection
{
    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT id FROM proteins WHERE accession = ?
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

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(string $accession): ResultSetInterface
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);
        $select_chains_sth = $this->pdo->prepare(self::SELECT_CHAINS_SQL);

        $select_protein_sth->execute([$accession]);

        if ($protein = $select_protein_sth->fetch()) {
            $select_chains_sth->execute([$protein['id']]);

            $chains = $select_chains_sth->fetchAll();

            return new ResultSet($chains);
        }

        throw new NotFoundException(
            sprintf('%s has no entry with accession \'%s\'', self::class, $accession)
        );
    }
}
