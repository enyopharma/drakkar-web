<?php

declare(strict_types=1);

use Domain\Actions\StoreDescriptionInterface;
use Domain\Actions\DeleteDescriptionInterface;
use Domain\Actions\PopulatePublicationInterface;
use Domain\Actions\UpdatePublicationStateInterface;

return [
    PopulatePublicationInterface::class => function ($container) {
        return new Domain\Actions\PopulatePublicationSql(
            $container->get(PDO::class),
            $container->get(Infrastructure\Efetch::class)
        );
    },

    UpdatePublicationStateInterface::class => function ($container) {
        return new Domain\Actions\UpdatePublicationStateSql(
            $container->get(PDO::class)
        );
    },

    StoreDescriptionInterface::class => function ($container) {
        return new Domain\Actions\StoreDescriptionSql(
            $container->get(PDO::class)
        );
    },

    DeleteDescriptionInterface::class => function ($container) {
        return new Domain\Actions\DeleteDescriptionSql(
            $container->get(PDO::class)
        );
    },
];
