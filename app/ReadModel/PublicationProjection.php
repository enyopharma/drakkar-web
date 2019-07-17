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

    public function descriptions(int $pmid): DescriptionProjection
    {
        return new DescriptionProjection($this->pdo, $this->run_id, $pmid);
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

        $keywords = $this->keywords();

        return ($publication = $select_publication_sth->fetch())
            ? new ArrayResultSet($this->formatted($publication, $keywords))
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

        $publications = [];

        $select_publications_sth->execute([$this->run_id, $this->state, $limit, $offset]);

        $keywords = $this->keywords();

        while ($publication = $select_publications_sth->fetch()) {
            $publications[] = $this->formatted($publication, $keywords);
        }

        return new Pagination(
            new ArrayResultSet(...$publications),
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

    private function formatted(array $publication, array $keywords = []): array
    {
        $metadata = ! is_null($publication['metadata'])
            ? json_decode($publication['metadata'], true)
            : [];

        $article = $metadata['PubmedArticle']['MedlineCitation']['Article'] ?? [];

        return [
            'run' => [
                'id' => $publication['run_id'],
                'type' => $publication['run_type'],
                'name' => $publication['run_name'],
            ],
            'run_id' => $publication['run_id'],
            'pmid' => $publication['pmid'],
            'state' => $publication['state'],
            'annotation' => $publication['annotation'],
            'title' => $article['ArticleTitle'] ?? $publication['pmid'],
            'journal' => $article['Journal']['Title'] ?? '',
            'abstract' => $this->abstract($article),
            'authors' => $this->authors($article),
            'keywords' => $keywords,
            'pending' => $publication['state'] == Publication::PENDING,
            'selected' => $publication['state'] == Publication::SELECTED,
            'discarded' => $publication['state'] == Publication::DISCARDED,
            'curated' => $publication['state'] == Publication::CURATED,
        ];
    }

    private function abstract(array $article): array
    {
        return is_array($article['Abstract']['AbstractText'] ?? [])
            ? $article['Abstract']['AbstractText'] ?? ['No abstract']
            : [$article['Abstract']['AbstractText'] ?? 'No abstract'];
    }

    private function author(array $author): string
    {
        return sprintf('%s %s', $author['LastName'], $author['Initials']);
    }

    private function authors(array $article): array
    {
        $authors = $article['AuthorList']['Author'] ?? [];

        try {
            return array_map(function (array $author) {
                return $this->author($author);
            }, $authors);
        }

        catch (\TypeError $e) {
            return [$this->author($authors)];
        }
    }
}
