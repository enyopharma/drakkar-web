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
    RunViewInterface::class => App\ReadModel\RunViewSql::class,
    FormViewInterface::class => App\ReadModel\FormViewSql::class,
    AssociationViewInterface::class => App\ReadModel\AssociationViewSql::class,
    PublicationViewInterface::class => App\ReadModel\PublicationViewSql::class,
    DescriptionViewInterface::class => App\ReadModel\DescriptionViewSql::class,
    MethodViewInterface::class => App\ReadModel\MethodViewSql::class,
    ProteinViewInterface::class => App\ReadModel\ProteinViewSql::class,
    PeptideViewInterface::class => App\ReadModel\PeptideViewSql::class,
    TaxonViewInterface::class => App\ReadModel\TaxonViewSql::class,
    DatasetViewInterface::class => App\ReadModel\DatasetViewSql::class,
];
