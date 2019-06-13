<?php declare(strict_types=1);

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use Enyo\Http\Extensions\Plates\UrlExtension;
use Enyo\Http\Extensions\Plates\AssetsExtension;

return [
    'parameters' => [
        'plates.manifest.path' => '%{app.root}/public/build/manifest.json',
        'plates.templates.path' => '%{app.root}/templates',
        'plates.templates.extension' => 'php',
    ],

    'mappers' => [
        'plates.extensions' => ExtensionInterface::class,
    ],

    'factories' => [
        Engine::class => function ($container) {
            return new Engine(...[
                $container->get('plates.templates.path'),
                $container->get('plates.templates.extension'),
            ]);
        },

        UrlExtension::class => function ($container) {
            return new UrlExtension(
                $container->get(Zend\Expressive\Helper\UrlHelper::class)
            );
        },

        AssetsExtension::class => function ($container) {
            return new AssetsExtension(
                $container->get('plates.manifest.path')
            );
        },
    ],

    'extensions' => [
        League\Plates\Engine::class => function ($container, League\Plates\Engine $engine) {
            $engine->addData([
                'session' => $container->get(Enyo\Http\Session::class),
            ]);

            $extensions = $container->get('plates.extensions');

            foreach ($extensions as $extension) {
                $engine->loadExtension($extension);
            }

            return $engine;
        },
    ],
];