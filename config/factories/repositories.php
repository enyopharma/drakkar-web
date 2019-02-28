<?php declare(strict_types=1);

use App\Repositories\RunRepository;
use App\Repositories\PublicationRepository;

use Enyo\Sql\StatementMap;

return [
    'factories' => [
        RunRepository::class => function ($container) {
            return new RunRepository(
                $container->get(StatementMap::class)
            );
        },

        PublicationRepository::class => function ($container) {
            return new PublicationRepository(
                $container->get(StatementMap::class)
            );
        },
    ],
];
