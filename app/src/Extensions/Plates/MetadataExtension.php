<?php

declare(strict_types=1);

namespace App\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

final class MetadataExtension implements ExtensionInterface
{
    public function register(Engine $engine): void
    {
        $engine->registerFunction('metadata', [$this, 'metadata']);
    }

    public function metadata(string $metadata = null): array
    {
        $default = [
            'title' => '',
            'journal' => '',
            'abstract' => ['Unknown format'],
            'authors' => [],
        ];

        try {
            $metadata = is_null($metadata) ? [] : json_decode($metadata, true);

            if (key_exists('PubmedArticle', $metadata)) {
                return array_merge($default, $this->article($metadata['PubmedArticle']['MedlineCitation']));
            }

            if (key_exists('PubmedBookArticle', $metadata)) {
                return array_merge($default, $this->book($metadata['PubmedBookArticle']['BookDocument']));
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

    private function title(mixed $title): string
    {
        return is_string($title) ? $title : '';
    }

    private function journal(mixed $journal): string
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
            return array_map([$this, 'author'], $authors);
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
