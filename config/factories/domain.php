<?php declare(strict_types=1);

return [
    'factories' => [
        App\Domain\InsertRun::class => function ($container) {
            return new App\Domain\InsertRun(
                $container->get(PDO::class)
            );
        },

        App\Domain\PopulateRun::class => function ($container) {
            return new App\Domain\PopulateRun(
                $container->get(PDO::class),
                $container->get(App\Services\Efetch::class)
            );
        },

        App\Domain\PopulatePublication::class => function ($container) {
            return new App\Domain\PopulatePublication(
                $container->get(PDO::class),
                $container->get(App\Services\Efetch::class)
            );
        },

        App\Domain\InsertDescription::class => function ($container) {
            return new App\Domain\InsertDescription(
                $container->get(PDO::class)
            );
        },

        App\Domain\UpdatePublicationState::class => function ($container) {
            return new App\Domain\UpdatePublicationState(
                $container->get(PDO::class)
            );
        },

        App\Domain\StartAlignment::class => function ($container) {
            return new App\Domain\StartAlignment(
                $container->get(Predis\Client::class)
            );
        },
    ],
];
