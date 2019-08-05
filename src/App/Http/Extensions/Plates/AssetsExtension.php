<?php declare(strict_types=1);

namespace App\Http\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

final class AssetsExtension implements ExtensionInterface
{
    private $manifest;

    private $map;

    public function __construct(string $manifest)
    {
        $this->manifest = $manifest;
        $this->map = null;
    }

    public function register(Engine $engine)
    {
        /** @var \League\Plates\callback */
        $asset = function (string $path) {
            $map = $this->cachedMap();

            if (array_key_exists($path, $map)) {
                return $map[$path];
            }

            throw new \LogicException(sprintf("Asset not found: '%s'.", $path));
        };

        $engine->registerFunction('asset', $asset);
    }

    private function cachedMap()
    {
        if (is_null($this->map)) {
            $this->map = $this->map();
        }

        return $this->map;
    }

    private function map()
    {
        if (file_exists($this->manifest)) {
            $contents = file_get_contents($this->manifest);

            return json_decode((string) $contents, true) ?? [];
        }

        throw new \LogicException(sprintf("Asset manifest file not found: '%s'.", $this->manifest));
    }
}
