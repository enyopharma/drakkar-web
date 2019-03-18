<?php declare(strict_types=1);

return [
    'factories' => [
        App\Domain\InsertRun::class => function ($container) {
            return new App\Domain\InsertRun(
                $container->get(PDO::class)
            );
        },

        App\Domain\SelectRun::class => function ($container) {
            return new App\Domain\SelectRun(
                $container->get(PDO::class)
            );
        },

        App\Domain\SelectRuns::class => function ($container) {
            return new App\Domain\SelectRuns(
                $container->get(PDO::class)
            );
        },

        App\Domain\UpdatePublicationState::class => function ($container) {
            return new App\Domain\UpdatePublicationState(
                $container->get(PDO::class)
            );
        },

        App\Domain\PopulateRun::class => function ($container) {
            return new App\Domain\PopulateRun(
                $container->get(PDO::class),
                $container->get(App\Domain\PopulatePublication::class)
            );
        },

        App\Domain\PopulatePublication::class => function ($container) {
            return new App\Domain\PopulatePublication(
                $container->get(PDO::class)
            );
        },
    ],
];
