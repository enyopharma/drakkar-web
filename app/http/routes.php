<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * Return the route definitions.
 *
 * @return array[]
 */
return function (): array {
    return [
        'GET /' => [
            'action' => Domain\Actions\CollectRuns::class,
            'responder' => App\Http\Responders\RunResponder::class,
        ],

        'GET /runs/{run_id:\d+}/publications' => [
            'action' => Domain\Actions\CollectPublications::class,
            'responder' => App\Http\Responders\PublicationResponder::class,
        ],

        'PUT /runs/{run_id:\d+}/publications/{pmid:\d+}' => [
            'action' => Domain\Actions\UpdatePublicationState::class,
            'responder' => App\Http\Responders\PublicationResponder::class,
        ],

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => [
            'action' => Domain\Actions\CollectDescriptions::class,
            'responder' => App\Http\Responders\DescriptionResponder::class,
        ],

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/create' => [
            'action' => Domain\Actions\SelectPublication::class,
            'responder' => App\Http\Responders\FormResponder::class,
        ],

        'GET /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}/edit' => [
            'action' => Domain\Actions\SelectDescription::class,
            'responder' => App\Http\Responders\FormResponder::class,
        ],

        'POST /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions' => [
            'action' => Domain\Actions\CreateDescription::class,
        ],

        'DELETE /runs/{run_id:\d+}/publications/{pmid:\d+}/descriptions/{id:\d+}' => [
            'action' => Domain\Actions\DeleteDescription::class,
        ],

        'GET /dataset/{type:hh|vh}' => [
            'action' => Domain\Actions\DownloadDataset::class,
            'responder' => App\Http\Responders\DatasetResponder::class,
        ],

        'GET /methods' => [
            'action' => Domain\Actions\SearchMethods::class,
        ],

        'GET /methods/{psimi_id}' => [
            'action' => Domain\Actions\SelectMethod::class,
        ],

        'GET /proteins' => [
            'action' => Domain\Actions\SearchProteins::class,
        ],

        'GET /proteins/{accession}' => [
            'action' => Domain\Actions\SelectProtein::class,
        ],

        'POST /jobs/alignments' => [
            'action' => Domain\Actions\StartAlignment::class,
        ],
    ];
};
