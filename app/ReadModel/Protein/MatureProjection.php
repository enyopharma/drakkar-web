<?php declare(strict_types=1);

namespace App\ReadModel\Protein;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\NotFoundException;
use Enyo\ReadModel\ResultSetInterface;

final class MatureProjection
{
    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT id FROM proteins WHERE accession = ?
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

    public function all(string $accession): ResultSetInterface
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);
        $select_matures_sth = $this->pdo->prepare(self::SELECT_MATURES_SQL);

        $select_protein_sth->execute([$accession]);

        if ($protein = $select_protein_sth->fetch()) {
            $select_matures_sth->execute([$protein['id']]);

            $matures = $select_matures_sth->fetchAll();

            return new ResultSet($matures);
        }

        throw new NotFoundException(
            sprintf('%s has no entry with accession \'%s\'', self::class, $accession)
        );
    }
}
