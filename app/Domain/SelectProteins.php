<?php declare(strict_types=1);

namespace App\Domain;

use Enyo\Data\ResultSet;

final class SelectProteins
{
    const SELECT_PROTEINS_SQL = <<<SQL
        SELECT accession, name, description
        FROM proteins
        WHERE type = ? AND %s
        LIMIT 20
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(string $type, string $q): DomainPayloadInterface
    {
        $parts = (array) preg_split('/\s+/', $q);

        $select_proteins_sth = $this->pdo->prepare(vsprintf(self::SELECT_PROTEINS_SQL, [
            implode(' AND ', array_pad([], count($parts), 'search ILIKE ?')),
        ]));

        $select_proteins_sth->execute(array_merge([$type], array_map(function ($part) {
            return '%' . $part . '%';
        }, $parts)));

        return new DomainSuccess([
            'proteins' => new ResultSet($select_proteins_sth),
        ]);
    }
}
