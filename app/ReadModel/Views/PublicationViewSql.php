<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

use App\Domain\Publication;

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

    public function pmid(int $run_id, int $pmid)
    {
        $select_publication_sth = $this->selectPublicationsQuery()
            ->where('p.pmid = ?')
            ->prepare();

        $select_publication_sth->execute([$run_id, $pmid]);

        if ($publication = $select_publication_sth->fetch()) {
            $keywords = $this->keywords();

            return $this->formatted($keywords, $publication);
        }

        return false;
    }

    public function all(int $run_id, string $state, int $limit, int $offset): array
    {
        $select_publications_sth = $this->selectPublicationsQuery()
            ->where('a.state = ?')
            ->orderby('a.updated_at ASC, a.id ASC')
            ->sliced()
            ->prepare();

        $select_publications_sth->execute([$run_id, $state, $limit, $offset]);

        $keywords = $this->keywords();

        $publications = [];

        while ($publication = $select_publications_sth->fetch()) {
            $publications[] = $this->formatted($keywords, $publication);
        }

        return $publications;
    }

    private function keywords(): array
    {
        $select_keywords_sth = Query::instance($this->pdo)
            ->select('*')
            ->from('keywords')
            ->prepare();

        $select_keywords_sth->execute();

        return (array) $select_keywords_sth->fetchAll();
    }

    private function formatted(array $keywords, array $publication): array
    {
        $raw = [
            'run_id' => $publication['run_id'],
            'pmid' => $publication['pmid'],
            'state' => $publication['state'],
            'annotation' => $publication['annotation'],
            'keywords' => $keywords,
            'pending' => $publication['state'] == Publication::PENDING,
            'selected' => $publication['state'] == Publication::SELECTED,
            'discarded' => $publication['state'] == Publication::DISCARDED,
            'curated' => $publication['state'] == Publication::CURATED,
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
        }

        catch (\Throwable $e) {
            return array_merge($raw, [
                'title' => '',
                'journal' => '',
                'abstract' => ['Unknown format'],
                'authors' => [],
            ]);
        }
    }

    private function article(array $metadata): array
    {
        return [
            'title' => $metadata['Article']['ArticleTitle'] ?? '',
            'journal' => $metadata['Article']['Journal']['Title'] ?? '',
            'abstract' => $this->abstract($metadata['Article']['Abstract'] ?? $metadata['OtherAbstract'] ?? []),
            'authors' => $this->authors($metadata['Article']['AuthorList'] ?? []),
        ];
    }

    private function book(array $metadata): array
    {
        return [
            'title' => $metadata['ArticleTitle'] ?? '',
            'journal' => $metadata['Book']['BookTitle'] ?? '',
            'abstract' => $this->abstract($metadata['Abstract']),
            'authors' => $this->authors($metadata['AuthorList']),
        ];
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
        return sprintf('%s %s', $author['LastName'], $author['Initials']);
    }
}
