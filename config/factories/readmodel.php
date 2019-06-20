<?php declare(strict_types=1);

use App\ReadModel\RunProjection;
use App\ReadModel\MethodProjection;
use App\ReadModel\ProteinProjection;
use App\ReadModel\InteractorProjection;
use App\ReadModel\PublicationProjection;
use App\ReadModel\DescriptionProjection;
use App\ReadModel\DescriptionSumupProjection;

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

        InteractorProjection::class => function ($container) {
            return new InteractorProjection(
                $container->get(PDO::class),
                $container->get(ProteinProjection::class)
            );
        },

        DescriptionProjection::class => function ($container) {
            return new DescriptionProjection(
                $container->get(PDO::class),
                $container->get(MethodProjection::class),
                $container->get(InteractorProjection::class)
            );
        },

        DescriptionSumupProjection::class => function ($container) {
            return new DescriptionSumupProjection(
                $container->get(PDO::class)
            );
        },
    ],
];
