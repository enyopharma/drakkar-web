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

        $engine->registerFunction('highlighted', function (string $str, string $type, array $patterns) {
            $map = [
                Run::HH => 'text-primary',
                Run::VH => 'text-danger',
            ];

            $replacement = sprintf('<span class="%s">$1</span>', $map[$type] ?? '');
            $replacements = array_pad([], count($patterns), $replacement);

            return preg_replace($patterns, $replacements, $str);
        });
    }
}
