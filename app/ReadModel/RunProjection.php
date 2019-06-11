<?php declare(strict_types=1);

namespace App\ReadModel;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\NotFoundException;
use Enyo\ReadModel\ResultSetInterface;

final class RunProjection
{
    const SELECT_RUN_SQL = <<<SQL
        SELECT *
        FROM runs
        WHERE populated = TRUE AND deleted_at IS NULL
        AND id = ?
SQL;

    const SELECT_RUNS_SQL = <<<SQL
        SELECT *
        FROM runs
        WHERE populated = TRUE AND deleted_at IS NULL
        ORDER BY created_at DESC, id DESC
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function id(int $id): array
    {
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);

        $select_run_sth->execute([$id]);

        if ($run = $select_run_sth->fetch()) {
            return $run;
        }

        throw new NotFoundException(
            sprintf('%s has no entry with id %s', self::class, $id)
        );
    }

    public function all(): ResultSetInterface
    {
        $select_runs_sth = $this->pdo->prepare(self::SELECT_RUNS_SQL);

        $select_runs_sth->execute();

        return new ResultSet($select_runs_sth->fetchAll());
    }
}
