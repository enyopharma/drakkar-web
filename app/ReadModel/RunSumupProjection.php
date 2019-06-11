<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Publication;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\ResultSetInterface;

final class RunSumupProjection
{
    const COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT run_id, state, COUNT(*) AS nb
        FROM associations
        GROUP BY run_id, state
SQL;

    private $pdo;

    private $runs;

    public function __construct(\PDO $pdo, RunProjection $runs)
    {
        $this->pdo = $pdo;
        $this->runs = $runs;
    }

    public function all(): ResultSetInterface
    {
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLICATIONS_SQL);

        $nbs = [];

        $count_publications_sth->execute();

        while ($row = $count_publications_sth->fetch()) {
            $nbs[$row['run_id']][$row['state']] = $row['nb'];
        }

        return $this->runs->all()->map(function ($run) use ($nbs) {
            return $run+= ['nbs' => [
                Publication::PENDING => $nbs[$run['id']][Publication::PENDING] ?? 0,
                Publication::SELECTED => $nbs[$run['id']][Publication::SELECTED] ?? 0,
                Publication::DISCARDED => $nbs[$run['id']][Publication::DISCARDED] ?? 0,
                Publication::CURATED => $nbs[$run['id']][Publication::CURATED] ?? 0,
            ]];
        });
    }
}
