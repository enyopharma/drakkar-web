<?php declare(strict_types=1);

return [
    'factories' => [
        App\Domain\Services\Efetch::class => function () {
            return new App\Domain\Services\Efetch;
        },

        App\Domain\InsertRun::class => function ($container) {
            return new App\Domain\InsertRun(
                $container->get(PDO::class)
            );
        },

        App\Domain\PopulateRun::class => function ($container) {
            return new App\Domain\PopulateRun(
                $container->get(PDO::class),
                $container->get(App\Domain\Services\Efetch::class)
            );
        },

        App\Domain\PopulatePublication::class => function ($container) {
            return new App\Domain\PopulatePublication(
                $container->get(PDO::class),
                $container->get(App\Domain\Services\Efetch::class)
            );
        },

        App\Domain\InsertDescription::class => function ($container) {
            return new App\Domain\InsertDescription(
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

        App\Domain\SelectMethods::class => function ($container) {
            return new App\Domain\SelectMethods(
                $container->get(PDO::class)
            );
        },

        App\Domain\SelectProtein::class => function ($container) {
            return new App\Domain\SelectProtein(
                $container->get(PDO::class)
            );
        },

        App\Domain\SelectProteins::class => function ($container) {
            return new App\Domain\SelectProteins(
                $container->get(PDO::class)
            );
        },
    ],
];
