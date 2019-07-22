<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Run;
use App\Domain\Protein;

final class DescriptionMapper
{
    public function __invoke(array $description): array
    {
        return [
            'run' => [
                'id' => $description['run_id'],
                'type' => $description['type'],
            ],
            'publication' => [
                'run_id' => $description['run_id'],
                'pmid' => $description['pmid'],
            ],
            'id' => $description['id'],
            'type' => $description['type'],
            'run_id' => $description['run_id'],
            'pmid' => $description['pmid'],
            'method' => [
                'psimi_id' => $description['psimi_id'],
            ],
            'interactor1' => [
                'type' => Protein::H,
                'name' => $description['name1'],
                'start' => $description['start1'],
                'stop' => $description['stop1'],
                'protein' => [
                    'accession' => $description['accession1'],
                ],
                'mapping' => json_decode($description['mapping1'], true),
            ],
            'interactor2' => [
                'type' => $description['type'] == Run::HH
                    ? Protein::H
                    : Protein::V,
                'name' => $description['name2'],
                'start' => $description['start2'],
                'stop' => $description['stop2'],
                'protein' => [
                    'accession' => $description['accession2'],
                ],
                'mapping' => json_decode($description['mapping2'], true),
            ],
            'created_at' => $this->date($description['created_at']),
            'deleted_at' => $this->date($description['deleted_at']),
            'deleted' => ! is_null($description['deleted_at']),
        ];
    }

    private function date(?string $date): string
    {
        if (is_null($date)) return '-';

        if (($time = strtotime($date)) !== false) {
            return date('Y - m - d', $time);
        }

        throw new \LogicException(
            sprintf('%s can\'t be converted to a time', $date)
        );
    }
}
