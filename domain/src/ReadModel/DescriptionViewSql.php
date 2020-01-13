<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class DescriptionViewSql implements DescriptionViewInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function selectDescriptionsQuery(): Query
    {
        return Query::instance($this->pdo)
            ->select('a.run_id, a.pmid')
            ->select('d.id, d.stable_id, d.created_at, d.deleted_at')
            ->select('m.psimi_id')
            ->select('i1.name AS name1, i1.start AS start1, i1.stop AS stop1, i1.mapping AS mapping1')
            ->select('i2.name AS name2, i2.start AS start2, i2.stop AS stop2, i2.mapping AS mapping2')
            ->select('p1.type AS type1, p1.accession AS accession1')
            ->select('p2.type AS type2, p2.accession AS accession2')
            ->from('associations AS a')
            ->from('descriptions AS d')
            ->from('methods AS m')
            ->from('interactors AS i1, interactors AS i2')
            ->from('proteins AS p1, proteins AS p2')
            ->where('a.id = d.association_id')
            ->where('m.id = d.method_id')
            ->where('i1.id = d.interactor1_id')
            ->where('i2.id = d.interactor2_id')
            ->where('p1.id = i1.protein_id')
            ->where('p2.id = i2.protein_id')
            ->where('a.run_id = ?')
            ->where('a.pmid = ?');
    }

    public function count(int $run_id, int $pmid): int
    {
        $count_descriptions_sth = Query::instance($this->pdo)
            ->select('COUNT(*)')
            ->from('associations AS a, descriptions AS d')
            ->where('a.id = d.association_id')
            ->where('a.run_id = ?')
            ->where('a.pmid = ?')
            ->prepare();

        $count_descriptions_sth->execute([$run_id, $pmid]);

        return ($nb = $count_descriptions_sth->fetchColumn()) ? (int) $nb : 0;
    }

    public function id(int $run_id, int $pmid, int $id): Statement
    {
        $select_description_sth = $this->selectDescriptionsQuery()
            ->where('d.id = ?')
            ->prepare();

        $select_description_sth->execute([$run_id, $pmid, $id]);

        return new Statement(
            $this->generator($select_description_sth)
        );
    }

    public function all(int $run_id, int $pmid, int $limit, int $offset): Statement
    {
        $select_descriptions_sth = $this->selectDescriptionsQuery()
            ->orderby('d.created_at DESC, d.id DESC')
            ->sliced()
            ->prepare();

        $select_descriptions_sth->execute([$run_id, $pmid, $limit, $offset]);

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
            'run_id' => $description['run_id'],
            'pmid' => $description['pmid'],
            'id' => $description['id'],
            'stable_id' => $description['stable_id'],
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
