<?php declare(strict_types=1);

namespace Http\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use App\Repositories\Publication;

final class HelpersExtension implements ExtensionInterface
{
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
    }
}
