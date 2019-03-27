<?php declare(strict_types=1);

namespace App\Domain;

final class PublicationCollection implements \IteratorAggregate
{
    private $type;

    private $publications;

    private $keywords;

    public function __construct(string $type, array $publications, array $keywords)
    {
        $this->type = $type;
        $this->publications = $publications;
        $this->keywords = $keywords;
    }

    public function getIterator()
    {
        $patterns = array_map([$this, 'pattern'], $this->keywords);

        foreach ($this->publications as $publication) {
            $metadata = json_decode($publication['metadata'] ?? [], true);

            $article = $metadata['PubmedArticle']['MedlineCitation']['Article'] ?? [];

            yield [
                'run_id' => $publication['run_id'],
                'pmid' => $publication['pmid'],
                'type' => $this->type,
                'state' => $publication['state'],
                'title' => $article['ArticleTitle'] ?? $publication['pmid'],
                'abstract' => $this->highlighted(
                    $article['Abstract']['AbstractText'] ?? 'No abstract',
                    $patterns
                ),
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
