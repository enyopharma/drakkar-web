<?php declare(strict_types=1);

namespace App\ReadModel\Protein;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\NotFoundException;
use Enyo\ReadModel\ResultSetInterface;

final class IsoformProjection
{
    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT id FROM proteins WHERE accession = ?
SQL;

    const SELECT_ISOFORMS_SQL = <<<SQL
        SELECT accession, sequence, is_canonical
        FROM sequences
        WHERE protein_id = ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(string $accession): ResultSetInterface
    {
        $select_protein_sth = $this->pdo->prepare(self::SELECT_PROTEIN_SQL);
        $select_isoforms_sth = $this->pdo->prepare(self::SELECT_ISOFORMS_SQL);

        $select_protein_sth->execute([$accession]);

        if ($protein = $select_protein_sth->fetch()) {
            $select_isoforms_sth->execute([$protein['id']]);

            $isoforms = $select_isoforms_sth->fetchAll();

            return new ResultSet($isoforms);
        }

        throw new NotFoundException(
            sprintf('%s has no entry with accession \'%s\'', self::class, $accession)
        );
    }
}
