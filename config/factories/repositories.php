<?php declare(strict_types=1);

use App\Repositories\RunRepository;
use App\Repositories\PublicationRepository;

return [
    'factories' => [
        RunRepository::class => function ($container) {
            return new RunRepository(
                $container->get(\PDO::class)
            );
        },

        PublicationRepository::class => function ($container) {
            return new PublicationRepository(
                $container->get(\PDO::class)
            );
        },
    ],
];
