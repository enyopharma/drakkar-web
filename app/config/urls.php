<?php

declare(strict_types=1);

return [
    'runs.index' => fn () => '/',
    'runs.publications.index' => fn (array $data) => vsprintf('/runs/%s/publications', [
        $data['id'],
    ]),
    'runs.publications.update' => fn (array $data) => vsprintf('/runs/%s/publications/%s', [
        $data['run_id'],
        $data['pmid'],
    ]),
    'runs.publications.descriptions.index' => fn (array $data) => vsprintf('/runs/%s/publications/%s/descriptions', [
        $data['run_id'],
        $data['pmid'],
    ]),
    'runs.publications.descriptions.create' => fn (array $data) => vsprintf('/runs/%s/publications/%s/descriptions/create', [
        $data['run_id'],
        $data['pmid'],
    ]),
    'runs.publications.descriptions.edit' => fn (array $data) => vsprintf('/runs/%s/publications/%s/descriptions/%s/edit', [
        $data['run_id'],
        $data['pmid'],
        $data['id'],
    ]),
    'publications.index' => fn () => '/publications',
    'dataset' => fn (array $data) => sprintf('/dataset/%s', $data['type']),
];
