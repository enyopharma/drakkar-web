<?php

declare(strict_types=1);

namespace App\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

final class HighlightExtension implements ExtensionInterface
{
    private ?array $keywords;

    const SELECT_KEYWORDS_SQL = <<<SQL
        SELECT * FROM keywords
    SQL;

    public function __construct(
        private \PDO $pdo,
    ) {
        $this->keywords = null;
    }

    public function register(Engine $engine): void
    {
        $engine->registerFunction('highlight', [$this, 'highlight']);
    }

    public function highlight(string $str): string
    {
        $keywords = $this->keywords();

        $patterns = [
            $this->pattern('g', $keywords),
            $this->pattern('v', $keywords),
        ];

        $replacements = [
            '<strong class="kv g">$0</strong>',
            '<strong class="kv v">$0</strong>',
        ];

        return is_null($highlighted = preg_replace($patterns, $replacements, $str)) ? $str : $highlighted;
    }

    private function pattern(string $type, array $keywords): string
    {
        $patterns = array_map(function ($k) {
                return '[A-Z0-9-]*' . $k['pattern'] . '[A-Z0-9-]*';
            }, array_filter($keywords, function ($k) use ($type) {
                return $k['type'] == $type;
            })
        );

        usort($patterns, function ($a, $b) { return strlen($b) - strlen($a); });

        return '/' . implode('|', $patterns) . '/i';
    }

    private function keywords(): array
    {
        if (!$this->keywords) {
            $select_keywords_sth = $this->pdo->prepare(self::SELECT_KEYWORDS_SQL);

            if ($select_keywords_sth === false) throw new \Exception;

            $select_keywords_sth->execute();

            $this->keywords = (array) $select_keywords_sth->fetchAll();
        }

        return $this->keywords;
    }
}
