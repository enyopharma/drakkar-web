<?php declare(strict_types=1);

use App\Domain\InsertRun;
use App\Domain\PopulateRun;
use App\Domain\StartAlignment;
use App\Domain\PopulatePublication;
use App\Domain\UpdatePublicationState;

return [
    'factories' => [
        InsertRun::class => function ($container) {
            return new InsertRun(
                $container->get(PDO::class)
            );
        },

        PopulateRun::class => function ($container) {
            return new PopulateRun(
                $container->get(PDO::class),
                $container->get(App\Services\Efetch::class)
            );
        },

        PopulatePublication::class => function ($container) {
            return new PopulatePublication(
                $container->get(PDO::class),
                $container->get(App\Services\Efetch::class)
            );
        },

        UpdatePublicationState::class => function ($container) {
            return new UpdatePublicationState(
                $container->get(PDO::class)
            );
        },

        StartAlignment::class => function ($container) {
            return new StartAlignment(
                $container->get(Predis\Client::class)
            );
        },
    ],
];
