<?php

declare(strict_types=1);

namespace App\Http\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

final class HelpersExtension implements ExtensionInterface
{
    private $map = [
        \Domain\Association::PENDING => [
            'header' => 'Pending publication',
            'empty' => 'There is no pending publication.',
            'classes' => [
                'text' => 'text-warning',
                'badge' => 'badge-warning',
            ],
        ],
        \Domain\Association::SELECTED => [
            'header' => 'Selected publication',
            'empty' => 'There is no selected publication.',
            'classes' => [
                'text' => 'text-primary',
                'badge' => 'badge-primary',
            ],
        ],
        \Domain\Association::DISCARDED => [
            'header' => 'Discarded publication',
            'empty' => 'There is no discarded publication.',
            'classes' => [
                'text' => 'text-danger',
                'badge' => 'badge-danger',
            ],
        ],
        \Domain\Association::CURATED => [
            'header' => 'Curated publication',
            'empty' => 'There is no curated publication.',
            'classes' => [
                'text' => 'text-success',
                'badge' => 'badge-success',
            ],
        ],
    ];

    public function register(Engine $engine)
    {
        /** @var \League\Plates\callback */
        $header = function (string $state) {
            return $this->map[$state]['header'] ?? '';
        };

        /** @var \League\Plates\callback */
        $empty = function (string $state) {
            return $this->map[$state]['empty'] ?? '';
        };

        /** @var \League\Plates\callback */
        $textclass = function (string $state) {
            return $this->map[$state]['classes']['text'] ?? '';
        };

        /** @var \League\Plates\callback */
        $badgeclass = function (string $state) {
            return $this->map[$state]['classes']['badge'] ?? '';
        };

        /** @var \League\Plates\callback */
        $highlighted = function (string $str, array $keywords) {
            if (count($keywords) > 0) {
                $patterns = [
                    $this->pattern('g', $keywords),
                    $this->pattern('v', $keywords),
                ];

                $replacements = [
                    '<strong class="kv g">$0</strong>',
                    '<strong class="kv v">$0</strong>',
                ];

                return preg_replace($patterns, $replacements, $str);
            }

            return $str;
        };

        $engine->registerFunction('header', $header);
        $engine->registerFunction('empty', $empty);
        $engine->registerFunction('textclass', $textclass);
        $engine->registerFunction('badgeclass', $badgeclass);
        $engine->registerFunction('highlighted', $highlighted);
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
