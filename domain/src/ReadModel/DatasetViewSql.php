<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class DatasetViewSql implements DatasetViewInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): Statement
    {
        $select_descriptions_sth = Query::instance($this->pdo)
            ->select('r.type')
            ->select('d.stable_id')
            ->select('a.pmid')
            ->select('m.psimi_id')
            ->select('i1.name AS name1, i1.start AS start1, i1.stop AS stop1, i1.mapping AS mapping1')
            ->select('i2.name AS name2, i2.start AS start2, i2.stop AS stop2, i2.mapping AS mapping2')
            ->select('p1.id AS protein1_id, p1.accession AS accession1')
            ->select('p2.id AS protein2_id, p2.accession AS accession2')
            ->from('runs AS r')
            ->from('associations AS a')
            ->from('descriptions AS d')
            ->from('methods AS m')
            ->from('interactors AS i1, interactors AS i2')
            ->from('proteins AS p1, proteins AS p2')
            ->where('r.id = a.run_id')
            ->where('a.id = d.association_id')
            ->where('m.id = d.method_id')
            ->where('i1.id = d.interactor1_id')
            ->where('i2.id = d.interactor2_id')
            ->where('p1.id = i1.protein_id')
            ->where('p2.id = i2.protein_id')
            ->where('a.state = \'curated\'')
            ->where('r.deleted_at IS NULL')
            ->where('d.deleted_at IS NULL')
            ->orderby('d.created_at DESC, d.id DESC')
            ->prepare();

        $select_descriptions_sth->execute();

        return new Statement(
            $this->generator($select_descriptions_sth)
        );
    }

    private function generator(\PDOStatement $sth): \Generator
    {
        while ($description = $sth->fetch()) {
            yield $this->formatted($description);
        }
    }

    private function formatted(array $description): array
    {
        return [
            'type' => $description['type'],
            'stable_id' => $description['stable_id'],
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
