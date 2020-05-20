<?php

declare(strict_types=1);

use App\ReadModel\RunViewInterface;
use App\ReadModel\MethodViewInterface;
use App\ReadModel\ProteinViewInterface;
use App\ReadModel\DatasetViewInterface;
use App\ReadModel\PublicationViewInterface;

return [
    RunViewInterface::class => fn ($container) => new App\ReadModel\RunViewSql(
        $container->get(PDO::class),
    ),

    MethodViewInterface::class => fn ($container) => new App\ReadModel\MethodViewSql(
        $container->get(PDO::class),
    ),

    ProteinViewInterface::class => fn ($container) => new App\ReadModel\ProteinViewSql(
        $container->get(PDO::class),
    ),

    PublicationViewInterface::class => fn ($container) => new App\ReadModel\PublicationViewSql(
        $container->get(PDO::class),
    ),

    DatasetViewInterface::class => fn ($container) => new App\ReadModel\DatasetViewSql(
        $container->get(PDO::class),
    ),
];
