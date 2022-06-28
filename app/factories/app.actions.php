<?php

declare(strict_types=1);

use App\Actions\StorePeptideInterface;
use App\Actions\StoreDescriptionInterface;
use App\Actions\DeleteDescriptionInterface;
use App\Actions\UpdatePublicationStateInterface;

return [
    StorePeptideInterface::class => fn ($container) => new App\Actions\StorePeptideSql(
        $container->get(PDO::class),
    ),

    StoreDescriptionInterface::class => fn ($container) => new App\Actions\StoreDescriptionSql(
        $container->get(PDO::class),
    ),

    DeleteDescriptionInterface::class => fn ($container) => new App\Actions\DeleteDescriptionSql(
        $container->get(PDO::class),
    ),

    UpdatePublicationStateInterface::class => fn ($container) => new App\Actions\UpdatePublicationStateSql(
        $container->get(PDO::class),
    ),
];
