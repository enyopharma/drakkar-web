<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Publication;

final class PublicationProjection implements ProjectionInterface
{
    const SELECT_FROM_PMID_SQL = <<<SQL
        SELECT r.id AS run_id, r.type AS run_type, r.name AS run_name, a.annotation, a.state, p.*
        FROM runs AS r, publications AS p, associations AS a
        WHERE r.id = a.run_id AND p.pmid = a.pmid
        AND a.run_id = ?
        AND p.pmid = ?
SQL;

    const PAGINATE_PUBLICATIONS_SQL = <<<SQL
        SELECT r.id AS run_id, r.type AS run_type, r.name AS run_name, a.annotation, a.state, p.*
        FROM runs AS r, publications AS p, associations AS a
        WHERE r.id = a.run_id AND p.pmid = a.pmid
        AND a.run_id = ?
        AND a.state = ?
        ORDER BY a.updated_at ASC, a.id ASC
        LIMIT ? OFFSET ?
SQL;

    const COUNT_PUBLCIATIONS_SQL = <<<SQL
        SELECT run_id, state, COUNT(*) AS nb
        FROM associations
        WHERE run_id = ? AND state = ?
        GROUP BY run_id, state
SQL;

    const SELECT_KEYWORDS_SQL = <<<SQL
        SELECT type, pattern FROM keywords
SQL;

    private $pdo;

    private $run_id;

    private $state;

    public function __construct(\PDO $pdo, int $run_id, string $state = Publication::PENDING)
    {
        $this->pdo = $pdo;
        $this->run_id = $run_id;
        $this->state = $state;
    }

    public function rset(array $criteria = []): ResultSetInterface
    {
        return key_exists('pmid', $criteria)
            ? $this->pmid((int) $criteria['pmid'])
            : $this->pagination(
                (int) ($criteria['page'] ?? 1),
                (int) ($criteria['limit'] ?? 20)
            );
    }

    private function pmid(int $pmid): ResultSetInterface
    {
        $select_publication_sth = $this->pdo->prepare(self::SELECT_FROM_PMID_SQL);

        $select_publication_sth->execute([$this->run_id, $pmid]);

        return ($publication = $select_publication_sth->fetch())
            ? new MappedResultSet(
                new ArrayResultSet($publication),
                new PublicationMapper($this->keywords())
            )
            : new EmptyResultSet(self::class, ['pmid' => $pmid]);
    }

    private function pagination(int $page, int $limit): ResultSetInterface
    {
        $offset = ($page - 1) * $limit;
        $total = $this->count();

        if ($page < 1 || ($offset > 0 && $total <= $offset)) {
            throw new \OutOfRangeException;
        }

        $select_publications_sth = $this->pdo->prepare(self::PAGINATE_PUBLICATIONS_SQL);

        $select_publications_sth->execute([$this->run_id, $this->state, $limit, $offset]);

        return new Pagination(
            new MappedResultSet(
                new ArrayResultSet(...$select_publications_sth->fetchAll()),
                new PublicationMapper($this->keywords())
            ),
            $total,
            $page,
            $limit
        );
    }

    private function count(): int
    {
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLCIATIONS_SQL);

        $count_publications_sth->execute([$this->run_id, $this->state]);

        return ($nb = $count_publications_sth->fetchColumn(2)) ? $nb : 0;
    }

    private function keywords(): array
    {
        $select_keywords_sth = $this->pdo->prepare(self::SELECT_KEYWORDS_SQL);

        $select_keywords_sth->execute();

        return $select_keywords_sth->fetchAll();
    }
}
