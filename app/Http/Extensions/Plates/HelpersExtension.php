<?php declare(strict_types=1);

namespace App\Http\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use App\Repositories\Publication;

final class HelpersExtension implements ExtensionInterface
{
    private $map = [
        Publication::PENDING => [
            'header' => 'Pending publication',
            'empty' => 'There is no pending publication.',
            'styles' => [
                'text' => 'text-warning',
            ],
        ],
        Publication::SELECTED => [
            'header' => 'Selected publication',
            'empty' => 'There is no selected publication.',
            'styles' => [
                'text' => 'text-primary',
            ],
        ],
        Publication::DISCARDED => [
            'header' => 'Discarded publication',
            'empty' => 'There is no discarded publication.',
            'styles' => [
                'text' => 'text-danger',
            ],
        ],
        Publication::CURATED => [
            'header' => 'Curated publication',
            'empty' => 'There is no curated publication.',
            'styles' => [
                'text' => 'text-success',
            ],
        ],
    ];

    public function register(Engine $engine)
    {
        $engine->registerFunction('pending', function () {
            return Publication::PENDING;
        });

        $engine->registerFunction('selected', function () {
            return Publication::SELECTED;
        });

        $engine->registerFunction('discarded', function () {
            return Publication::DISCARDED;
        });

        $engine->registerFunction('curated', function () {
            return Publication::CURATED;
        });

        $engine->registerFunction('isPending', function (string $state) {
            return $state === Publication::PENDING;
        });

        $engine->registerFunction('isSelected', function (string $state) {
            return $state === Publication::SELECTED;
        });

        $engine->registerFunction('isDiscarded', function (string $state) {
            return $state === Publication::DISCARDED;
        });

        $engine->registerFunction('isCurated', function (string $state) {
            return $state === Publication::CURATED;
        });

        $engine->registerFunction('stateMap', function (string $state) {
            return $this->map[$state] ?? [];
        });
    }
}
