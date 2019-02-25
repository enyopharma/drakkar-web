<?php declare(strict_types=1);

namespace Http\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use App\Repositories\Association;

final class HelpersExtension implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('pending', function () {
            return Association::PENDING;
        });

        $engine->registerFunction('selected', function () {
            return Association::SELECTED;
        });

        $engine->registerFunction('discarded', function () {
            return Association::DISCARDED;
        });

        $engine->registerFunction('curated', function () {
            return Association::CURATED;
        });

        $engine->registerFunction('isPending', function (string $state) {
            return $state === Association::PENDING;
        });

        $engine->registerFunction('isSelected', function (string $state) {
            return $state === Association::SELECTED;
        });

        $engine->registerFunction('isDiscarded', function (string $state) {
            return $state === Association::DISCARDED;
        });

        $engine->registerFunction('isCurated', function (string $state) {
            return $state === Association::CURATED;
        });
    }
}
