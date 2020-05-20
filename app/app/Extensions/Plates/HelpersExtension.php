<?php

declare(strict_types=1);

namespace App\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

final class HelpersExtension implements ExtensionInterface
{
    private array $map = [
        'pending' => [
            'header' => 'Pending publication',
            'empty' => 'There is no pending publication.',
            'classes' => [
                'text' => 'text-warning',
                'badge' => 'badge-warning',
            ],
        ],
        'selected' => [
            'header' => 'Selected publication',
            'empty' => 'There is no selected publication.',
            'classes' => [
                'text' => 'text-primary',
                'badge' => 'badge-primary',
            ],
        ],
        'discarded' => [
            'header' => 'Discarded publication',
            'empty' => 'There is no discarded publication.',
            'classes' => [
                'text' => 'text-danger',
                'badge' => 'badge-danger',
            ],
        ],
        'curated' => [
            'header' => 'Curated publication',
            'empty' => 'There is no curated publication.',
            'classes' => [
                'text' => 'text-success',
                'badge' => 'badge-success',
            ],
        ],
    ];

    public function register(Engine $engine): void
    {
        $engine->registerFunction('header', \Closure::fromCallable([$this, 'header']));
        $engine->registerFunction('empty', \Closure::fromCallable([$this, 'empty']));
        $engine->registerFunction('textclass', \Closure::fromCallable([$this, 'textclass']));
        $engine->registerFunction('badgeclass', \Closure::fromCallable([$this, 'badgeclass']));
        $engine->registerFunction('highlighted', \Closure::fromCallable([$this, 'highlighted']));
    }

    private function header(string $state): string
    {
        return $this->map[$state]['header'] ?? '';
    }

    private function empty(string $state): string
    {
        return $this->map[$state]['empty'] ?? '';
    }

    private function textclass(string $state): string
    {
        return $this->map[$state]['classes']['text'] ?? '';
    }

    private function badgeclass(string $state): string
    {
        return $this->map[$state]['classes']['badge'] ?? '';
    }

    private function highlighted(string $str, array $keywords): string
    {
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
}
