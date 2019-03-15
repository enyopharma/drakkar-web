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

        App\Domain\UpdatePublicationMetadata::class => function ($container) {
            return new App\Domain\UpdatePublicationMetadata(
                $container->get(PDO::class)
            );
        },
    ],
];
