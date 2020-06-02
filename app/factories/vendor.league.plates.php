<?php

declare(strict_types=1);

use League\Plates\Engine;

return [
    Engine::class => function ($container) {
        $engine = new Engine(__DIR__ . '/../templates', 'php');

        $generator = $container->get(App\Routing\UrlGenerator::class);

        $assets = new App\Extensions\Plates\AssetsExtension(
            __DIR__ . '/../public/build/manifest.json',
        );

        $pagination = new App\Extensions\Plates\PaginationExtension;

        $metadata = new App\Extensions\Plates\MetadataExtension;

        $highlight = new App\Extensions\Plates\HighlightExtension(
            $container->get(\PDO::class),
        );

        $engine->registerFunction('url', [$generator, 'generate']);

        $engine->loadExtension($assets);
        $engine->loadExtension($pagination);
        $engine->loadExtension($metadata);
        $engine->loadExtension($highlight);

        return $engine;
    },
];
