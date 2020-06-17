<?php

declare(strict_types=1);

return [
    'runs.index' => fn () => '/',

    'runs.publications.index' => function (array $run) {
        return sprintf('/runs/%s/publications', $run['id']);
    },

    'runs.publications.update' => function (array $publication) {
        return vsprintf('/runs/%s/publications/%s', [
            $publication['run_id'],
            $publication['pmid']
        ]);
    },

    'runs.publications.descriptions.index' => function (array $publication) {
        return vsprintf('/runs/%s/publications/%s/descriptions', [
            $publication['run_id'],
            $publication['pmid'],
        ]);
    },

    'runs.publications.descriptions.create' => function (array $publication) {
        return vsprintf('/runs/%s/publications/%s/descriptions/create', [
            $publication['run_id'],
            $publication['pmid'],
        ]);
    },

    'runs.publications.descriptions.edit' => function (array $description) {
        return vsprintf('/runs/%s/publications/%s/descriptions/%s/edit', [
            $description['run_id'],
            $description['pmid'],
            $description['id'],
        ]);
    },

    'publications.index' => fn () => '/publications',

    'dataset' => fn (array $data) => sprintf('/dataset/%s', $data['type']),
];
