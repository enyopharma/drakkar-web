<?php declare(strict_types=1);

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use Zend\Expressive\Helper\UrlHelper;

use Utils\Http\Extensions\Plates\UrlExtension;
use Utils\Http\Extensions\Plates\AssetsExtension;

return [
    'parameters' => [
        'plates.manifest.path' => '%{app.root}/public/build/manifest.json',
        'plates.templates.path' => '%{app.root}/templates',
        'plates.templates.extension' => 'php',
    ],

    'factories' => [
        UrlExtension::class => function ($container) {
            return new UrlExtension(
                $container->get(UrlHelper::class)
            );
        },

        AssetsExtension::class => function ($container) {
            return new AssetsExtension(
                $container->get('plates.manifest.path')
            );
        },
    ],

    'extensions' => [
        Engine::class => function ($container, Engine $engine) {
            $xs[] = $container->get(UrlExtension::class);
            $xs[] = $container->get(AssetsExtension::class);

            foreach ($xs as $extension) {
                $engine->loadExtension($extension);
            }

            return $engine;
        },
    ],
];
