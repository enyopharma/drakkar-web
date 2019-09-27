<?php

declare(strict_types=1);

use Domain\Actions\CreateRun;
use Domain\Actions\CollectRuns;
use Domain\Actions\PopulateRun;
use Domain\Actions\SelectMethod;
use Domain\Actions\SearchMethods;
use Domain\Actions\SelectProtein;
use Domain\Actions\SearchProteins;
use Domain\Actions\StartAlignment;
use Domain\Actions\DownloadDataset;
use Domain\Actions\SelectPublication;
use Domain\Actions\SelectDescription;
use Domain\Actions\CreateDescription;
use Domain\Actions\DeleteDescription;
use Domain\Actions\CollectPublications;
use Domain\Actions\CollectDescriptions;
use Domain\Actions\PopulatePublication;
use Domain\Actions\UpdatePublicationState;

return [
    CreateRun::class => function ($container) {
        return new CreateRun(
            $container->get(PDO::class)
        );
    },

    PopulateRun::class => function ($container) {
        return new PopulateRun(
            $container->get(PDO::class),
            $container->get(Infrastructure\Efetch::class)
        );
    },

    PopulatePublication::class => function ($container) {
        return new PopulatePublication(
            $container->get(PDO::class),
            $container->get(Infrastructure\Efetch::class)
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

    CreateDescription::class => function ($container) {
        return new CreateDescription(
            $container->get(PDO::class),
            new Domain\Services\StableId
        );
    },

    DeleteDescription::class => function ($container) {
        return new DeleteDescription(
            $container->get(PDO::class)
        );
    },

    SelectMethod::class => function ($container) {
        return new SelectMethod(
            $container->get(Domain\ReadModel\MethodViewInterface::class)
        );
    },

    SearchMethods::class => function ($container) {
        return new SearchMethods(
            $container->get(Domain\ReadModel\MethodViewInterface::class)
        );
    },

    SelectProtein::class => function ($container) {
        return new SelectProtein(
            $container->get(Domain\ReadModel\ProteinViewInterface::class)
        );
    },

    SearchProteins::class => function ($container) {
        return new SearchProteins(
            $container->get(Domain\ReadModel\ProteinViewInterface::class)
        );
    },

    CollectRuns::class => function ($container) {
        return new CollectRuns(
            $container->get(Domain\ReadModel\RunViewInterface::class)
        );
    },

    SelectPublication::class => function ($container) {
        return new SelectPublication(
            $container->get(Domain\ReadModel\RunViewInterface::class),
            $container->get(Domain\ReadModel\PublicationViewInterface::class)
        );
    },

    SelectDescription::class => function ($container) {
        return new SelectDescription(
            $container->get(Domain\ReadModel\RunViewInterface::class),
            $container->get(Domain\ReadModel\PublicationViewInterface::class),
            $container->get(Domain\ReadModel\DescriptionViewInterface::class)
        );
    },

    CollectPublications::class => function ($container) {
        return new CollectPublications(
            $container->get(Domain\ReadModel\RunViewInterface::class),
            $container->get(Domain\ReadModel\PublicationViewInterface::class)
        );
    },

    CollectDescriptions::class => function ($container) {
        return new CollectDescriptions(
            $container->get(Domain\ReadModel\RunViewInterface::class),
            $container->get(Domain\ReadModel\PublicationViewInterface::class),
            $container->get(Domain\ReadModel\DescriptionViewInterface::class)
        );
    },

    DownloadDataset::class => function ($container) {
        return new DownloadDataset(
            $container->get(Domain\ReadModel\DatasetViewInterface::class)
        );
    },
];
