<?php declare(strict_types=1);

use App\ReadModel\RunProjection;
use App\ReadModel\MethodProjection;
use App\ReadModel\ProteinProjection;
use App\ReadModel\RunSumupProjection;
use App\ReadModel\InteractorProjection;
use App\ReadModel\DescriptionProjection;
use App\ReadModel\PublicationProjection;
use App\ReadModel\PrecurationProjection;

return [
    'factories' => [
        RunProjection::class => function ($container) {
            return new RunProjection(
                $container->get(PDO::class)
            );
        },

        PublicationProjection::class => function ($container) {
            return new PublicationProjection(
                $container->get(PDO::class)
            );
        },

        MethodProjection::class => function ($container) {
            return new MethodProjection(
                $container->get(PDO::class)
            );
        },

        ProteinProjection::class => function ($container) {
            return new ProteinProjection(
                $container->get(PDO::class)
            );
        },

        RunSumupProjection::class => function ($container) {
            return new RunSumupProjection(
                $container->get(PDO::class),
                $container->get(RunProjection::class)
            );
        },

        PrecurationProjection::class => function ($container) {
            return new PrecurationProjection(
                $container->get(RunProjection::class),
                $container->get(PublicationProjection::class)
            );
        },

        DescriptionProjection::class => function ($container) {
            return new DescriptionProjection(
                $container->get(PDO::class),
                $container->get(MethodProjection::class),
                $container->get(InteractorProjection::class)
            );
        },
    ],
];
