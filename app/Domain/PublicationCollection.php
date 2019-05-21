<?php declare(strict_types=1);

namespace App\Domain;

final class PublicationCollection implements \IteratorAggregate
{
    private $publications;

    private $keywords;

    public function __construct(array $publications, array $keywords)
    {
        $this->publications = $publications;
        $this->keywords = $keywords;
    }

    public function getIterator()
    {
        $patterns = array_map([$this, 'pattern'], $this->keywords);

        foreach ($this->publications as $publication) {
            $metadata = json_decode($publication['metadata'] ?? [], true);

            $article = $metadata['PubmedArticle']['MedlineCitation']['Article'] ?? [];

            $abstract = is_array($article['Abstract']['AbstractText'] ?? [])
                ? $article['Abstract']['AbstractText'] ?? ['No abstract']
                : [$article['Abstract']['AbstractText'] ?? 'No abstract'];

            $abstract = array_map(function (string $abstract) use ($patterns) {
                return $this->highlighted($abstract, $patterns);
            }, $abstract);

            yield [
                'run_id' => $publication['run_id'],
                'pmid' => $publication['pmid'],
                'state' => $publication['state'],
                'annotation' => $publication['annotation'],
                'title' => $article['ArticleTitle'] ?? $publication['pmid'],
                'abstract' => $abstract,
                'journal' => $article['Journal']['Title'] ?? '',
                'authors' => array_map(function (array $author) {
                    return sprintf('%s %s', $author['LastName'], $author['Initials']);
                }, $article['AuthorList']['Author'] ?? [])
            ];
        }
    }

    private function pattern(string $keyword): string
    {
        return '/(' . str_replace('*', '[^\s(),]*', $keyword) . ')/i';
    }

    private function highlighted(string $abstract, array $patterns): string
    {
        return preg_replace($patterns, '**$1**', $abstract);
    }
}
