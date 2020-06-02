<?php

declare(strict_types=1);

use League\Plates\Engine;

return [
    Engine::class => function ($container) {
        $engine = new Engine(__DIR__ . '/../templates', 'php');

        $generator = $container->get(App\Helpers\UrlGenerator::class);

        $engine->registerFunction('url', [$generator, 'generate']);

        $engine->loadExtension(
            new App\Extensions\Plates\AssetsExtension(
                __DIR__ . '/../public/build/manifest.json',
            )
        );

        $engine->loadExtension(
            new App\Extensions\Plates\HelpersExtension,
        );

        $engine->loadExtension(
            new App\Extensions\Plates\MetadataExtension,
        );

        $engine->loadExtension(
            new App\Extensions\Plates\HighlightExtension(
                $container->get(\PDO::class),
            ),
        );

        $engine->loadExtension(
            new App\Extensions\Plates\PaginationExtension,
        );

        return $engine;
    },
];
