<?php

declare(strict_types=1);

use League\Plates\Engine;

return [
    Engine::class => function ($container) {
        $engine = new Engine(__DIR__ . '/../templates', 'php');

        $generator = $container->get(App\Helpers\UrlGenerator::class);

        $pagination = new App\Widgets\PaginationWidget($engine);

        $engine->registerFunction('url', [$generator, 'generate']);
        $engine->registerFunction('pagination', [$pagination, 'render']);

        $engine->loadExtension(
            new App\Extensions\Plates\AssetsExtension(
                __DIR__ . '/../public/build/manifest.json',
            )
        );

        $engine->loadExtension(
            new App\Extensions\Plates\MetadataExtension,
        );

        $engine->loadExtension(
            new App\Extensions\Plates\HighlightExtension(
                $container->get(\PDO::class),
            ),
        );

        return $engine;
    },
];
