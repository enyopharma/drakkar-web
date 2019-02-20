<?php declare(strict_types=1);

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use Http\Extensions\Plates\Assets;

return [
    'parameters' => [
        'plates.manifest.path' => '%{app.root}/public/build/manifest.json',
        'plates.templates.path' => '%{app.root}/templates',
        'plates.templates.extension' => 'php',
    ],

    'factories' => [
        Assets::class => function ($container) {
            $manifest = $container->get('plates.manifest.path');

            return new Assets($manifest);
        },
    ],

    'extensions' => [
        Engine::class => function ($container, Engine $engine) {
            $assets = $container->get(Assets::class);

            $engine->loadExtension($assets);

            return $engine;
        },
    ],
];
