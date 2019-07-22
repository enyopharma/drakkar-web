<?php declare(strict_types=1);

namespace App\ReadModel;

final class DatasetMapper
{
    public function __invoke(array $description): array
    {
        return [
            'publication' => [
                'pmid' => $description['pmid'],
            ],
            'method' => [
                'psimi_id' => $description['psimi_id'],
            ],
            'interactor1' => [
                'protein' => [
                    'accession' => $description['accession1'],
                ],
                'name' => $description['name1'],
                'start' => $description['start1'],
                'stop' => $description['stop1'],
                'mapping' => json_decode($description['mapping1'], true),
            ],
            'interactor2' => [
                'protein' => [
                    'accession' => $description['accession2'],
                ],
                'name' => $description['name2'],
                'start' => $description['start2'],
                'stop' => $description['stop2'],
                'mapping' => json_decode($description['mapping2'], true),
            ],
        ];
    }
}
