<?php declare(strict_types=1);

use App\ReadModel\RunProjection;
use App\ReadModel\MethodProjection;
use App\ReadModel\ProteinProjection;
use App\ReadModel\PublicationProjection;
use App\ReadModel\DescriptionProjection;

return [
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

    DescriptionProjection::class => function ($container) {
        return new DescriptionProjection(
            $container->get(PDO::class)
        );
    },
];
