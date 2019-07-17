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
            'keywords' => $this->keywords,
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
