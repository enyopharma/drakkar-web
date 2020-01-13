<?php

declare(strict_types=1);

use Domain\Services\StoreDescriptionService;
use Domain\Services\DeleteDescriptionService;
use Domain\Services\PopulatePublicationService;
use Domain\Services\UpdatePublicationStateService;

return [
    PopulatePublicationService::class => function ($container) {
        return new PopulatePublicationService(
            $container->get(PDO::class),
            $container->get(Infrastructure\Efetch::class)
        );
    },

    UpdatePublicationStateService::class => function ($container) {
        return new UpdatePublicationStateService(
            $container->get(PDO::class)
        );
    },

    StoreDescriptionService::class => function ($container) {
        return new StoreDescriptionService(
            $container->get(PDO::class)
        );
    },

    DeleteDescriptionService::class => function ($container) {
        return new DeleteDescriptionService(
            $container->get(PDO::class)
        );
    },
];
