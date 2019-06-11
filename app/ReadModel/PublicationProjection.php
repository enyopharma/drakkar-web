<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Publication;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\Pagination;
use Enyo\ReadModel\NotFoundException;
use Enyo\ReadModel\OverflowException;
use Enyo\ReadModel\UnderflowException;
use Enyo\ReadModel\ResultSetInterface;

final class PublicationProjection
{
    const SELECT_FROM_PMID_SQL = <<<SQL
        SELECT a.run_id, a.annoations, a.state, p.*
        FROM publications AS p, associations AS a
        WHERE p.pmid = a.pmid
        AND a.run_id = ?
        AND p.pmid = ?
SQL;

    const PAGINATE_PUBLICATIONS_SQL = <<<SQL
        SELECT a.run_id, a.annotation, a.state, p.*
        FROM publications AS p, associations AS a
        WHERE p.pmid = a.pmid
        AND a.run_id = ?
        AND a.state = ?
        ORDER BY a.updated_at DESC, a.id ASC
        LIMIT ? OFFSET ?
SQL;

    const COUNT_PUBLCIATIONS_SQL = <<<SQL
        SELECT COUNT(*) FROM associations WHERE run_id = ? AND state = ?
SQL;

    const SELECT_KEYWORDS_SQL = <<<SQL
        SELECT k.pattern
        FROM runs AS r, keywords AS k
        WHERE r.type = k.type
        AND r.id = ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function pmid(int $run_id, int $pmid): array
    {
        $select_publication_sth = $this->pdo->prepare(self::SELECT_FROM_PMID_SQL);

        $select_publication_sth->execute([$run_id, $pmid]);

        $patterns = $this->patterns($run_id);

        if ($publication = $select_publication_sth->fetch()) {
            return $this->formatted($publication, $patterns);
        }

        throw new NotFoundException(
            sprintf('%s has no entry with run_id %s and pmid %s', self::class, $pmid)
        );
    }

    public function pagination(int $run_id, string $state, int $page = 1, int $limit = 20): ResultSetInterface
    {
        $offset = ($page - 1) * $limit;
        $total = $this->count($run_id, $state);

        if ($page < 1) {
            throw new UnderflowException;
        }

        if ($offset > 0 && $total <= $offset) {
            throw new OverflowException;
        }

        $select_publications_sth = $this->pdo->prepare(self::PAGINATE_PUBLICATIONS_SQL);

        $select_publications_sth->execute([$run_id, $state, $limit, $offset]);

        $patterns = $this->patterns($run_id);

        $publications = [];

        while ($publication = $select_publications_sth->fetch()) {
            $publications[] = $this->formatted($publication, $patterns);
        }

        return new Pagination(new ResultSet($publications), $total, $page, $limit);
    }

    public function count(int $run_id, string $state): int
    {
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLCIATIONS_SQL);

        $count_publications_sth->execute([$run_id, $state]);

        return ($nb = $count_publications_sth->fetchColumn()) ? $nb : 0;
    }

    private function patterns(int $run_id): array
    {
        $select_keywords_sth = $this->pdo->prepare(self::SELECT_KEYWORDS_SQL);

        $select_keywords_sth->execute([$run_id]);

        $patterns = [];

        while ($keyword = $select_keywords_sth->fetch()) {
            $patterns[] = '/(' . str_replace('*', '[^\s(),]*', $keyword['pattern']) . ')/i';
        }

        return $patterns;
    }

    private function formatted(array $publication, array $patterns = []): array
    {
        $metadata = json_decode($publication['metadata'] ?? [], true);

        $article = $metadata['PubmedArticle']['MedlineCitation']['Article'] ?? [];

        return [
            'run_id' => $publication['run_id'],
            'pmid' => $publication['pmid'],
            'state' => $publication['state'],
            'annotation' => $publication['annotation'],
            'title' => $article['ArticleTitle'] ?? $publication['pmid'],
            'journal' => $article['Journal']['Title'] ?? '',
            'abstract' => $this->abstract($article),
            'authors' => $this->authors($article),
            'patterns' => $patterns,
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
