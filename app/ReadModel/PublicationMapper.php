<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Publication;

final class PublicationMapper
{
    private $keywords;

    public function __construct(array $keywords)
    {
        $this->keywords = $keywords;
    }

    public function __invoke(array $publication): array
    {
        $raw = [
            'run' => [
                'id' => $publication['run_id'],
                'type' => $publication['run_type'],
                'name' => $publication['run_name'],
            ],
            'run_id' => $publication['run_id'],
            'pmid' => $publication['pmid'],
            'state' => $publication['state'],
            'annotation' => $publication['annotation'],
            'keywords' => $this->keywords,
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
