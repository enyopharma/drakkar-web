<?php declare(strict_types=1);

namespace App\Domain;

use Enyo\Data\ResultSet;

final class SelectMethods
{
    const SELECT_METHODS_SQL = <<<SQL
        SELECT psimi_id, name
        FROM methods
        WHERE %s
        LIMIT 20
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(string $q): DomainPayloadInterface
    {
        $parts = (array) preg_split('/\s+/', $q);

        $select_methods_sth = $this->pdo->prepare(vsprintf(self::SELECT_METHODS_SQL, [
            implode(' AND ', array_pad([], count($parts), 'search ILIKE ?')),
        ]));

        $select_methods_sth->execute(array_map(function ($part) {
            return '%' . $part . '%';
        }, $parts));

        return new DomainSuccess([
            'methods' => new ResultSet($select_methods_sth),
        ]);
    }
}
