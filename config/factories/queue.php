<?php declare(strict_types=1);

use App\Domain\PopulatePublication;
use App\Queue\Jobs\PopulatePublicationHandler;

return [
    'factories' => [
        PopulatePublicationHandler::class => function ($container) {
            return new PopulatePublicationHandler(
                $container->get(PopulatePublication::class)
            );
        },
    ],
];
