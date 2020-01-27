<?php

declare(strict_types=1);

use Domain\ReadModel\RunViewInterface;
use Domain\ReadModel\MethodViewInterface;
use Domain\ReadModel\ProteinViewInterface;
use Domain\ReadModel\DatasetViewInterface;
use Domain\ReadModel\PublicationViewInterface;

return [
    RunViewInterface::class => function ($container) {
        return new Domain\ReadModel\RunViewSql(
            $container->get(PDO::class)
        );
    },

    MethodViewInterface::class => function ($container) {
        return new Domain\ReadModel\MethodViewSql(
            $container->get(PDO::class)
        );
    },

    ProteinViewInterface::class => function ($container) {
        return new Domain\ReadModel\ProteinViewSql(
            $container->get(PDO::class)
        );
    },

    PublicationViewInterface::class => function ($container) {
        return new Domain\ReadModel\PublicationViewSql(
            $container->get(PDO::class)
        );
    },

    DatasetViewInterface::class => function ($container) {
        return new Domain\ReadModel\DatasetViewSql(
            $container->get(PDO::class)
        );
    },
];
