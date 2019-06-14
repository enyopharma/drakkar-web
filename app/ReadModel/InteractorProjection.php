<?php declare(strict_types=1);

namespace App\ReadModel;

use Enyo\ReadModel\NotFoundException;

final class InteractorProjection
{
    const SELECT_FROM_ID_SQL = <<<SQL
        SELECT * FROM interactors WHERE id = ?
SQL;

    private $pdo;

    private $protein;

    public function __construct(\PDO $pdo, ProteinProjection $protein)
    {
        $this->pdo = $pdo;
        $this->protein = $protein;
    }

    public function id(int $id): array
    {
        $select_interactor_sth = $this->pdo->prepare(self::SELECT_FROM_ID_SQL);

        $select_interactor_sth->execute([$id]);

        if ($interactor = $select_interactor_sth->fetch()) {
            return [
                'name' => $interactor['name'],
                'start' => $interactor['start'],
                'stop' => $interactor['stop'],
                'protein' => $this->protein->id($interactor['protein_id']),
                'mapping' => json_decode($interactor['mapping'], true),
            ];
        }

        throw new NotFoundException(
            sprintf('%s has no entry with id %s', self::class, $id)
        );
    }
}
