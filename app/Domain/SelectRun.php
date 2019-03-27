<?php declare(strict_types=1);

namespace App\Domain;

use Enyo\Data\ResultSet;
use Enyo\Data\Pagination;

final class SelectRun
{
    const NOT_FOUND = 0;
    const INVALID_STATE = 1;
    const UNDERFLOW = 2;
    const OVERFLOW = 3;

    CONST LIMIT = 20;

    const SELECT_RUN_STH = <<<SQL
        SELECT * FROM runs WHERE populated IS TRUE AND id = ?
SQL;

    const SELECT_KEYWORDS_SQL = <<<SQL
        SELECT pattern FROM keywords WHERE type = ?
SQL;

    const COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT COUNT(*) FROM associations WHERE run_id = ? AND state = ?
SQL;

    const SELECT_PUBLICATIONS_SQL = <<<SQL
        SELECT a.run_id, a.state, p.*
        FROM publications AS p, associations AS a
        WHERE p.pmid = a.pmid
        AND a.run_id = ?
        AND a.state = ?
        LIMIT ? OFFSET ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(int $id, string $state, int $page = 1): DomainPayloadInterface
    {
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_STH);
        $select_keywords_sth = $this->pdo->prepare(self::SELECT_KEYWORDS_SQL);
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLICATIONS_SQL);
        $select_publications_sth = $this->pdo->prepare(self::SELECT_PUBLICATIONS_SQL);

        $select_run_sth->execute([$id]);

        if (! $run = $select_run_sth->fetch()) {
            return new DomainPayload(self::NOT_FOUND);
        }

        if (! in_array($state, Publication::STATES)) {
            return new DomainPayload(self::INVALID_STATE);
        }

        if ($page < 1) {
            return new DomainPayload(self::UNDERFLOW);
        }

        // select the keywords associated to the curation run type.
        $select_keywords_sth->execute([$run['type']]);

        $keywords = $select_keywords_sth->fetchAll(\PDO::FETCH_COLUMN);

        // select the curation runs publications number for each state.
        foreach (Publication::STATES as $s) {
            $count_publications_sth->execute([$run['id'], $s]);

            $nbs[$s] = ($nb = $count_publications_sth->fetchColumn()) ? $nb : 0;
        }

        // select a slice of the curation runs publications with the given state.
        $offset = ($page - 1) * self::LIMIT;

        if ($offset != 0 && $nbs[$state] <= $offset) {
            return new DomainPayload(self::OVERFLOW, [
                'max' => (int) ($nbs[$state]/self::LIMIT),
            ]);
        }

        $select_publications_sth->execute([$run['id'], $state, self::LIMIT, $offset]);

        $publications = $select_publications_sth->fetchAll();

        // Return a success with a paginated publication collection.
        return new DomainSuccess([
            'run' => $run,
            'nbs' => $nbs,
            'publications' => new Pagination(
                new ResultSet(
                    new PublicationCollection($run['type'], $publications, $keywords)
                ),
                $nbs[$state],
                $page,
                self::LIMIT
            ),
        ]);
    }
}
