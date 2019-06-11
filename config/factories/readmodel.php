<?php declare(strict_types=1);

return [
    'factories' => [
        App\ReadModel\RunProjection::class => function ($container) {
            return new App\ReadModel\RunProjection(
                $container->get(PDO::class)
            );
        },

        App\ReadModel\PublicationProjection::class => function ($container) {
            return new App\ReadModel\PublicationProjection(
                $container->get(PDO::class)
            );
        },

        App\ReadModel\MethodProjection::class => function ($container) {
            return new App\ReadModel\MethodProjection(
                $container->get(PDO::class)
            );
        },

        App\ReadModel\ProteinProjection::class => function ($container) {
            return new App\ReadModel\ProteinProjection(
                $container->get(PDO::class)
            );
        },

        App\ReadModel\RunSumupProjection::class => function ($container) {
            return new App\ReadModel\RunSumupProjection(
                $container->get(PDO::class),
                $container->get(App\ReadModel\RunProjection::class)
            );
        },

        App\ReadModel\PrecurationProjection::class => function ($container) {
            return new App\ReadModel\PrecurationProjection(
                $container->get(App\ReadModel\RunProjection::class),
                $container->get(App\ReadModel\PublicationProjection::class)
            );
        },
    ],
];
