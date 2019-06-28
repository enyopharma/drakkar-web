<?php declare(strict_types=1);

namespace App\Http\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use App\Domain\Run;
use App\Domain\Publication;

final class HelpersExtension implements ExtensionInterface
{
    private $map = [
        Publication::PENDING => [
            'header' => 'Pending publication',
            'empty' => 'There is no pending publication.',
            'classes' => [
                'text' => 'text-warning',
                'badge' => 'badge-warning',
            ],
        ],
        Publication::SELECTED => [
            'header' => 'Selected publication',
            'empty' => 'There is no selected publication.',
            'classes' => [
                'text' => 'text-primary',
                'badge' => 'badge-primary',
            ],
        ],
        Publication::DISCARDED => [
            'header' => 'Discarded publication',
            'empty' => 'There is no discarded publication.',
            'classes' => [
                'text' => 'text-danger',
                'badge' => 'badge-danger',
            ],
        ],
        Publication::CURATED => [
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
        $engine->registerFunction('header', function (string $state) {
            return $this->map[$state]['header'] ?? '';
        });

        $engine->registerFunction('empty', function (string $state) {
            return $this->map[$state]['empty'] ?? '';
        });

        $engine->registerFunction('textclass', function (string $state) {
            return $this->map[$state]['classes']['text'] ?? '';
        });

        $engine->registerFunction('badgeclass', function (string $state) {
            return $this->map[$state]['classes']['badge'] ?? '';
        });

        $engine->registerFunction('highlighted', function (string $str, array $keywords) {
            $patterns = [
                $this->pattern('g', $keywords),
                $this->pattern('v', $keywords),
            ];

            $replacements = [
                '<strong class="kv g">$0</strong>',
                '<strong class="kv v">$0</strong>',
            ];

            return preg_replace($patterns, $replacements, $str);
        });
    }

    private function pattern(string $type, array $keywords): string
    {
        $patterns = array_map(function ($k) use ($type) {
                return '[A-Z0-9-]*' . $k['pattern'] . '[A-Z0-9-]*';
            }, array_filter($keywords, function ($k) use ($type) {
                return $k['type'] == $type;
            })
        );

        usort($patterns, function ($a, $b) { return strlen($b) - strlen($a); });

        return '/' . implode('|', $patterns) . '/i';
    }
}
