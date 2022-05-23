<?php

declare(strict_types=1);

use App\ReadModel\RunViewInterface;
use App\ReadModel\FormViewInterface;
use App\ReadModel\TaxonViewInterface;
use App\ReadModel\MethodViewInterface;
use App\ReadModel\ProteinViewInterface;
use App\ReadModel\PeptideViewInterface;
use App\ReadModel\DatasetViewInterface;
use App\ReadModel\AssociationViewInterface;
use App\ReadModel\PublicationViewInterface;
use App\ReadModel\DescriptionViewInterface;

return [
    RunViewInterface::class => fn ($container) => new App\ReadModel\RunViewSql(
        $container->get(PDO::class),
    ),

    FormViewInterface::class => fn ($container) => new App\ReadModel\FormViewSql(
        $container->get(PDO::class),
    ),

    AssociationViewInterface::class => fn ($container) => new App\ReadModel\AssociationViewSql(
        $container->get(PDO::class),
    ),

    PublicationViewInterface::class => fn ($container) => new App\ReadModel\PublicationViewSql(
        $container->get(PDO::class),
    ),

    DescriptionViewInterface::class => fn ($container) => new App\ReadModel\DescriptionViewSql(
        $container->get(PDO::class),
    ),

    MethodViewInterface::class => fn ($container) => new App\ReadModel\MethodViewSql(
        $container->get(PDO::class),
    ),

    ProteinViewInterface::class => fn ($container) => new App\ReadModel\ProteinViewSql(
        $container->get(PDO::class),
    ),

    PeptideViewInterface::class => fn ($container) => new App\ReadModel\PeptideViewSql(
        $container->get(PDO::class),
    ),

    TaxonViewInterface::class => fn ($container) => new App\ReadModel\TaxonViewSql(
        $container->get(PDO::class),
    ),

    DatasetViewInterface::class => fn ($container) => new App\ReadModel\DatasetViewSql(
        $container->get(PDO::class),
    ),
];
