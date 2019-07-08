<?php declare(strict_types=1);

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use App\Http\Extensions\Plates\HelpersExtension;
use Enyo\Http\Extensions\Plates\UrlExtension;
use Enyo\Http\Extensions\Plates\AssetsExtension;

return [
    Engine::class => function ($container) {
        $path = sprintf('%s/templates', $container->get('app.root'));

        $engine = new Engine($path, 'php');

        $data = $container->get('league.plates.data');
        $extensions = $container->get('league.plates.extensions');

        $engine->addData($data);

        array_map([$engine, 'loadExtension'], $extensions);

        return $engine;
    },

    'league.plates.data' => function ($container) {
        return [
            'session' => $container->get(Enyo\Http\Session::class),
            'pending' => App\Domain\Publication::PENDING,
            'selected' => App\Domain\Publication::SELECTED,
            'discarded' => App\Domain\Publication::DISCARDED,
            'curated' => App\Domain\Publication::CURATED,
        ];
    },

    'league.plates.extensions' => function ($container) {
        return [
            $container->get(AssetsExtension::class),
            $container->get(UrlExtension::class),
            $container->get(HelpersExtension::class),
        ];
    },

    AssetsExtension::class => function ($container) {
        $path = sprintf('%s/public/build/manifest.json', $container->get('app.root'));

        return new AssetsExtension($path);
    },

    UrlExtension::class => function ($container) {
        return new UrlExtension(
            $container->get(Zend\Expressive\Helper\UrlHelper::class)
        );
    },

    HelpersExtension::class => function () {
        return new HelpersExtension;
    },
];
