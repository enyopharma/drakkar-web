<?php declare(strict_types=1);

namespace App\Domain;

final class Drakkar
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function run(): Run
    {

    }

    public function runs(): ResultSet
    {
        $select_runs_sth = $this->selectRunsSth();
        $count_publications_sth = $this->countPublicationsSth();

        $select_runs_sth->execute();

        $runs = $select_run_sth->fetchAll();

        foreach (Publication::STATES as $state) {
            $count_publications_sth->execute($state);

            $nbs[$state] = $count_publications_sth->fetchAll(
                \PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE
            );
        }

        return new ResultSet(new RunCollection($runs, $nbs));
    }
}
