<?php

declare(strict_types=1);

use Domain\Services\PublicationMetadataService;

return [
    PublicationMetadataService::class => function ($container) {
        return new PublicationMetadataService(
            $container->get(PDO::class),
            $container->get(Infrastructure\Efetch::class)
        );
    },
];
