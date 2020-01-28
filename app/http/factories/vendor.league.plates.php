<?php

declare(strict_types=1);

use League\Plates\Engine;

return [
    Engine::class => function ($container) {
        $engine = new Engine(__DIR__ . '/../templates', 'php');

        $engine->addData([
            'pending' => Domain\Publication::PENDING,
            'selected' => Domain\Publication::SELECTED,
            'discarded' => Domain\Publication::DISCARDED,
            'curated' => Domain\Publication::CURATED,
        ]);

        $engine->loadExtension(
            new App\Http\Extensions\Plates\UrlExtension(
                $container->get(App\Http\UrlGenerator::class)
            )
        );

        $engine->loadExtension(
            new App\Http\Extensions\Plates\AssetsExtension(
                __DIR__ . '/../../../public/build/manifest.json'
            )
        );

        $engine->loadExtension(
            new App\Http\Extensions\Plates\HelpersExtension
        );

        $engine->loadExtension(
            new App\Http\Extensions\Plates\MetadataExtension
        );

        $engine->loadExtension(
            new App\Http\Extensions\Plates\HighlightExtension(
                $container->get(\PDO::class)
            )
        );

        $engine->loadExtension(
            new App\Http\Extensions\Plates\PaginationExtension
        );

        return $engine;
    },
];
