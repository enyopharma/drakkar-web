<?php

declare(strict_types=1);

use League\Plates\Engine;

return [
    Engine::class => function ($container) {
        $engine = new Engine(__DIR__ . '/../resources/templates', 'php');

        $package = new Symfony\Component\Asset\Package(
            new Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy(
                __DIR__ . '/../public/build/manifest.json',
            )
        );

        $generator = $container->get(Quanta\Http\UrlGenerator::class);

        $pagination = new App\Extensions\Plates\PaginationExtension;

        $metadata = new App\Extensions\Plates\MetadataExtension;

        $highlight = new App\Extensions\Plates\HighlightExtension(
            $container->get(\PDO::class),
        );

        $engine->registerFunction('asset', [$package, 'getUrl']);
        $engine->registerFunction('url', [$generator, 'generate']);

        $engine->loadExtension($pagination);
        $engine->loadExtension($metadata);
        $engine->loadExtension($highlight);

        return $engine;
    },
];
