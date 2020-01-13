<?php

declare(strict_types=1);

use Domain\Services\DeleteDescriptionService;
use Domain\Services\PopulatePublicationService;

return [
    PopulatePublicationService::class => function ($container) {
        return new PopulatePublicationService(
            $container->get(PDO::class),
            $container->get(Infrastructure\Efetch::class)
        );
    },

    DeleteDescriptionService::class => function ($container) {
        return new DeleteDescriptionService(
            $container->get(PDO::class)
        );
    },
];
