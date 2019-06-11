<?php declare(strict_types=1);

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use Zend\Expressive\Helper\UrlHelper;

use Enyo\Http\Session;
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
            $engine->addData(['session' => $container->get(Session::class)]);

            $extensions = $container->get('plates.extensions');

            foreach ($extensions as $extension) {
                $engine->loadExtension($extension);
            }

            return $engine;
        },
    ],
];
