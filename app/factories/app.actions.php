<?php

declare(strict_types=1);

use App\Actions\StoreRunInterface;
use App\Actions\PopulateRunInterface;
use App\Actions\StoreDescriptionInterface;
use App\Actions\DeleteDescriptionInterface;
use App\Actions\PopulatePublicationInterface;
use App\Actions\UpdatePublicationStateInterface;

return [
    StoreRunInterface::class => fn ($container) => new App\Actions\StoreRunSql(
        $container->get(PDO::class),
    ),

    PopulateRunInterface::class => fn ($container) => new App\Actions\PopulateRunSql(
        $container->get(PDO::class),
    ),

    UpdatePublicationStateInterface::class => fn ($container) => new App\Actions\UpdatePublicationStateSql(
        $container->get(PDO::class),
    ),

    PopulatePublicationInterface::class => fn ($container) => new App\Actions\PopulatePublicationSql(
        $container->get(PDO::class),
        new App\Services\Efetch,
    ),

    StoreDescriptionInterface::class => fn ($container) => new App\Actions\StoreDescriptionSql(
        $container->get(PDO::class),
    ),

    DeleteDescriptionInterface::class => fn ($container) => new App\Actions\DeleteDescriptionSql(
        $container->get(PDO::class),
    ),
];
