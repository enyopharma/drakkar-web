<?php declare(strict_types=1);

namespace App\Domain;

use Enyo\Data\ResultSet;

final class SelectRuns
{
    const SELECT_RUNS_SQL = <<<SQL
        SELECT * FROM runs
SQL;

    const COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT a.run_id, COUNT(*)
        FROM publications AS p, associations AS a
        WHERE p.id = a.publication_id
        AND a.state = ?
        GROUP BY a.run_id
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(): DomainPayloadInterface
    {
        $select_runs_sth = $this->pdo->prepare(self::SELECT_RUNS_SQL);
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLICATIONS_SQL);

        $select_runs_sth->execute();

        $runs = $select_runs_sth->fetchAll();

        foreach (Publication::STATES as $state) {
            $count_publications_sth->execute([$state]);

            $nbs[$state] = $count_publications_sth->fetchAll(
                \PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE
            );
        }

        return new DomainSuccess([
            'runs' => new ResultSet(new RunCollection($runs, $nbs)),
        ]);
    }
}
