<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use App\Http\Responders\RunResponder;
use App\Http\Responders\DatasetResponder;
use App\Http\Responders\PublicationResponder;
use App\Http\Responders\DescriptionResponder;

/**
 * Return the route definitions.
 *
 * @param callable $factory
 * @return array[]
 */
return function (callable $factory): array {
    return [
        'GET /' => [
            'name' => 'runs.index',
            'handler' => $factory(Domain\Actions\CollectRuns::class, RunResponder::class),
        ],

        'GET /runs/{run_id:\d+}/publications' => [
            'name' => 'runs.publications.index',
            'handler' => $factory(Domain\Actions\CollectPublications::class, PublicationResponder::class),
        ],

        'PUT /runs/{run_id:\d+}/publications/{pmid:\d+}' => [
            'name' => 'runs.publications.update',
            'handler' => $factory(Domain\Actions\UpdatePublicationState::class, PublicationResponder::class),
        ],

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => [
            'name' => 'runs.publications.descriptions.index',
            'handler' => $factory(Domain\Actions\CollectDescriptions::class, DescriptionResponder::class),
        ],

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create' => [
            'name' => 'runs.publications.descriptions.create',
            'handler' => $factory(Domain\Actions\SelectPublication::class, DescriptionResponder::class),
        ],

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit' => [
            'name' => 'runs.publications.descriptions.edit',
            'handler' => $factory(Domain\Actions\SelectDescription::class, DescriptionResponder::class),
        ],

        'POST /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => [
            'handler' => $factory(Domain\Actions\CreateDescription::class, DescriptionResponder::class),
        ],

        'DELETE /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}' => [
            'handler' => $factory(Domain\Actions\DeleteDescription::class, DescriptionResponder::class),
        ],

        'GET /dataset' => [
            'name' => 'dataset',
            'handler' => $factory(Domain\Actions\DownloadDataset::class, DatasetResponder::class),
        ],

        'GET /methods' => [
            'handler' => $factory(Domain\Actions\SearchMethods::class),
        ],

        'GET /methods/{psimi_id}' => [
            'handler' => $factory(Domain\Actions\SelectMethod::class),
        ],

        'GET /proteins' => [
            'handler' => $factory(Domain\Actions\SearchProteins::class),
        ],

        'GET /proteins/{accession}' => [
            'handler' => $factory(Domain\Actions\SelectProtein::class),
        ],

        'POST /jobs/alignments' => [
            'handler' => $factory(Domain\Actions\StartAlignment::class),
        ],
    ];
};
