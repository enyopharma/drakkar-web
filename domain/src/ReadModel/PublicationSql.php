<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class PublicationSql implements PublicationInterface
{
    private $pdo;

    private $run_id;

    private $pmid;

    private $state;

    private $metadata;

    private $data;

    /**
     * @var array|null
     */
    private $keywords;

    const SELECT_KEYWORDS_SQL = <<<SQL
        SELECT * FROM keywords
SQL;

    public function __construct(\PDO $pdo, int $run_id, int $pmid, string $state, string $metadata = null, array $data = [])
    {
        $this->pdo = $pdo;
        $this->run_id = $run_id;
        $this->pmid = $pmid;
        $this->state = $state;
        $this->metadata = $metadata;
        $this->data = $data;
        $this->keywords = null;
    }

    public function data(): array
    {
        $data = [
            'pmid' => $this->pmid,
            'state' => $this->state,
            \Domain\Publication::PENDING => $this->data['state'] === \Domain\Publication::PENDING,
            \Domain\Publication::SELECTED => $this->data['state'] === \Domain\Publication::SELECTED,
            \Domain\Publication::DISCARDED => $this->data['state'] === \Domain\Publication::DISCARDED,
            \Domain\Publication::CURATED => $this->data['state'] === \Domain\Publication::CURATED,
            'keywords' => $this->keywords(),
        ] + $this->metadata();

        return $data + $this->data + [
            'url' => [
                'run_id' => $this->run_id,
                'pmid' => $this->pmid,
            ],
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->data();
    }

    public function descriptions(): DescriptionViewInterface
    {
        return new DescriptionViewSql($this->pdo, $this->run_id, $this->pmid);
    }

    private function keywords(): array
    {
        if (! $this->keywords) {
            $select_keywords_sth = $this->pdo->prepare(self::SELECT_KEYWORDS_SQL);

            $select_keywords_sth->execute();

            $this->keywords = (array) $select_keywords_sth->fetchAll();
        }

        return $this->keywords;
    }

    private function metadata(): array
    {
        $default = [
            'title' => '',
            'journal' => '',
            'abstract' => ['Unknown format'],
            'authors' => [],
        ];

        try {
            $this->metadata = is_null($this->metadata) ? [] : json_decode($this->metadata, true);

            if (key_exists('PubmedArticle', $this->metadata)) {
                return array_merge($default, $this->article($this->metadata['PubmedArticle']['MedlineCitation']));
            }

            if (key_exists('PubmedBookArticle', $this->metadata)) {
                return array_merge($default, $this->book($this->metadata['PubmedBookArticle']['BookDocument']));
            }

            return $default;
        }

        catch (\Throwable $e) {
            return $default;
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
