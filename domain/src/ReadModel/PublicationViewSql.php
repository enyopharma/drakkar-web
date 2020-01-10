<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class PublicationViewSql implements PublicationViewInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function selectPublicationsQuery(): Query
    {
        return Query::instance($this->pdo)
            ->select('a.run_id, p.pmid, a.state, a.annotation, p.metadata')
            ->from('publications AS p, associations AS a')
            ->where('p.pmid = a.pmid')
            ->where('a.run_id = ?');
    }

    public function count(int $run_id, string $state): int
    {
        $count_publications_sth = Query::instance($this->pdo)
            ->select('COUNT(*)')
            ->from('publications AS p, associations AS a')
            ->where('p.pmid = a.pmid')
            ->where('a.run_id = ?')
            ->where('a.state = ?')
            ->prepare();

        $count_publications_sth->execute([$run_id, $state]);

        return ($nb = $count_publications_sth->fetchColumn()) ? (int) $nb : 0;
    }

    public function pmid(int $run_id, int $pmid): Statement
    {
        $select_publication_sth = $this->selectPublicationsQuery()
            ->where('p.pmid = ?')
            ->prepare();

        $select_publication_sth->execute([$run_id, $pmid]);

        return new Statement(
            $this->generator($select_publication_sth)
        );
    }

    public function all(int $run_id, string $state, int $limit, int $offset): Statement
    {
        $select_publications_sth = $this->selectPublicationsQuery()
            ->where('a.state = ?')
            ->orderby('a.updated_at ASC, a.id ASC')
            ->sliced()
            ->prepare();

        $select_publications_sth->execute([$run_id, $state, $limit, $offset]);

        return new Statement(
            $this->generator($select_publications_sth)
        );
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        $select_keywords_sth = Query::instance($this->pdo)
            ->select('*')
            ->from('keywords')
            ->prepare();

        $select_keywords_sth->execute();

        $keywords = (array) $select_keywords_sth->fetchAll();

        while ($publication = $sth->fetch()) {
            yield $this->formatted($keywords, $publication);
        }
    }

    private function formatted(array $keywords, array $publication): array
    {
        $raw = [
            'run_id' => $publication['run_id'],
            'pmid' => $publication['pmid'],
            'state' => $publication['state'],
            'title' => '',
            'journal' => '',
            'abstract' => ['Unknown format'],
            'authors' => [],
            'annotation' => $publication['annotation'],
            'keywords' => $keywords,
            'pending' => $publication['state'] == \Domain\Association::PENDING,
            'selected' => $publication['state'] == \Domain\Association::SELECTED,
            'discarded' => $publication['state'] == \Domain\Association::DISCARDED,
            'curated' => $publication['state'] == \Domain\Association::CURATED,
        ];

        try {
            $metadata = ! is_null($publication['metadata'])
                ? json_decode($publication['metadata'], true)
                : [];

            if (key_exists('PubmedArticle', $metadata)) {
                return array_merge($raw, $this->article($metadata['PubmedArticle']['MedlineCitation']));
            }

            if (key_exists('PubmedBookArticle', $metadata)) {
                return array_merge($raw, $this->book($metadata['PubmedBookArticle']['BookDocument']));
            }

            return $raw;
        }

        catch (\Throwable $e) {
            return $raw;
        }
    }

    private function article(array $metadata): array
    {
        return [
            'title' => $this->title($metadata['Article']['ArticleTitle'] ?? ''),
            'journal' => $this->journal($metadata['Article']['Journal']['Title'] ?? ''),
            'abstract' => $this->abstract($metadata['Article']['Abstract'] ?? $metadata['OtherAbstract'] ?? []),
            'authors' => $this->authors($metadata['Article']['AuthorList'] ?? []),
        ];
    }

    private function book(array $metadata): array
    {
        return [
            'title' => $this->title($metadata['ArticleTitle'] ?? ''),
            'journal' => $this->journal($metadata['Book']['BookTitle'] ?? ''),
            'abstract' => $this->abstract($metadata['Abstract']),
            'authors' => $this->authors($metadata['AuthorList']),
        ];
    }

    /**
     * @param mixed $title
     */
    private function title($title): string
    {
        return is_string($title) ? $title : '';
    }

    /**
     * @param mixed $journal
     */
    private function journal($journal): string
    {
        return is_string($journal) ? $journal : '';
    }

    private function abstract(array $abstract): array
    {
        $text = $abstract['AbstractText'] ?? null;

        if (is_string($text)) {
            return [$text];
        }

        if (is_array($text)) {
            return array_filter($text, 'is_string');
        }

        return ['No abstract'];
    }

    private function authors(array $list): array
    {
        $authors = $list['Author'] ?? [];

        if (count($authors) == 0) {
            return ['No author'];
        }

        if (key_exists('CollectiveName', $authors)) {
            return [$authors['CollectiveName']];
        }

        try {
            return array_map(function (array $author) {
                return $this->author($author);
            }, $authors);
        }

        catch (\TypeError $e) {
            return [$this->author($authors)];
        }
    }

    private function author(array $author): string
    {
        if (key_exists('LastName', $author) && key_exists('Initials', $author)) {
            return sprintf('%s %s', $author['LastName'], $author['Initials']);
        }

        if (key_exists('CollectiveName', $author)) {
            return $author['CollectiveName'];
        }

        return '';
    }
}
