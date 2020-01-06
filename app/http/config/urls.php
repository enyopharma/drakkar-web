<?php

declare(strict_types=1);

return [
    'runs.index' => function (array $data) {
        return '/';
    },
    'runs.publications.index' => function (array $data) {
        return sprintf('/runs/%s/publications', $data['run_id']);
    },
    'runs.publications.update' => function (array $data) {
        return sprintf('/runs/%s/publications/%s', $data['run_id'], $data['pmid']);
    },
    'runs.publications.descriptions.index' => function (array $data) {
        return sprintf('/runs/%s/publications/%s/descriptions', $data['run_id'], $data['pmid']);
    },
    'runs.publications.descriptions.create' => function (array $data) {
        return sprintf('/runs/%s/publications/%s/descriptions/create', $data['run_id'], $data['pmid']);
    },
    'runs.publications.descriptions.edit' => function (array $data) {
        return sprintf('/runs/%s/publications/%s/descriptions/%s/edit', $data['run_id'], $data['pmid'], $data['id']);
    },
    'dataset' => function (array $data) {
        return sprintf('/dataset/%s', $data['type']);
    },
];
