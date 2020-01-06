<?php

declare(strict_types=1);

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use App\Http\Extensions\Plates\UrlExtension;
use App\Http\Extensions\Plates\AssetsExtension;
use App\Http\Extensions\Plates\HelpersExtension;
use App\Http\Extensions\Plates\PaginationExtension;

return [
    Engine::class => function ($container) {
        $engine = new Engine(__DIR__ . '/../templates', 'php');

        $data = $container->get('league.plates.data');
        $extensions = $container->get('league.plates.extensions');

        $engine->addData($data);

        array_map([$engine, 'loadExtension'], $extensions);

        return $engine;
    },

    'league.plates.data' => function ($container) {
        return [
            'pending' => Domain\Association::PENDING,
            'selected' => Domain\Association::SELECTED,
            'discarded' => Domain\Association::DISCARDED,
            'curated' => Domain\Association::CURATED,
        ];
    },

    'league.plates.extensions' => function ($container) {
        return [
            $container->get(UrlExtension::class),
            $container->get(AssetsExtension::class),
            $container->get(HelpersExtension::class),
            $container->get(PaginationExtension::class),
        ];
    },

    UrlExtension::class => function ($container) {
        return new UrlExtension(
            $container->get(App\Http\Helpers\UrlHelper::class)
        );
    },

    AssetsExtension::class => function ($container) {
        return new AssetsExtension(__DIR__ . '/../../../public/build/manifest.json');
    },

    HelpersExtension::class => function () {
        return new HelpersExtension;
    },

    PaginationExtension::class => function () {
        return new PaginationExtension;
    },
];
