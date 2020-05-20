<?php

declare(strict_types=1);

namespace App\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

final class AssetsExtension implements ExtensionInterface
{
    private ?array $map = null;

    private string $manifest;

    public function __construct(string $manifest)
    {
        $this->manifest = $manifest;
    }

    public function register(Engine $engine): void
    {
        $engine->registerFunction('asset', [$this, 'asset']);
    }

    public function asset(string $path): string
    {
        $map = $this->cachedMap();

        if (array_key_exists($path, $map)) {
            return '/' . $map[$path];
        }

        throw new \LogicException(sprintf("Asset not found: '%s'.", $path));
    }

    private function cachedMap(): array
    {
        if (! $this->map) {
            $this->map = $this->map();
        }

        return $this->map;
    }

    private function map(): array
    {
        if (file_exists($this->manifest)) {
            $contents = file_get_contents($this->manifest);

            return json_decode((string) $contents, true) ?? [];
        }

        throw new \LogicException(sprintf("Asset manifest file not found: '%s'.", $this->manifest));
    }
}
