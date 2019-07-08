<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Run;
use App\Domain\Protein;

final class DescriptionProjection
{
    const SELECT_DESCRIPTION_SQL = <<<SQL
        SELECT r.id AS run_id, r.type, a.pmid,
            d.id, m.psimi_id,
            i1.name AS name1, i1.start AS start1, i1.stop AS stop1, i1.mapping AS mapping1,
            i2.name AS name2, i2.start AS start2, i2.stop AS stop2, i2.mapping AS mapping2,
            p1.id AS protein1_id, p1.accession AS accession1,
            p2.id AS protein2_id, p2.accession AS accession2
        FROM runs AS r,
            associations AS a,
            descriptions AS d,
            methods AS m,
            interactors AS i1, interactors AS i2,
            proteins AS p1, proteins AS p2
        WHERE r.id = a.run_id
            AND a.id = d.association_id
            AND m.id = d.method_id
            AND i1.id = d.interactor1_id
            AND i2.id = d.interactor2_id
            AND p1.id = i1.protein_id
            AND p2.id = i2.protein_id
            AND a.run_id = ?
            AND a.pmid = ?
            AND d.id = ?
SQL;

    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT r.id AS run_id, r.type, a.pmid,
            d.id, d.created_at, d.deleted_at,
            m.psimi_id, m.name AS method_name,
            i1.name AS name1, i1.start AS start1, i1.stop AS stop1, i1.mapping AS mapping1,
            i2.name AS name2, i2.start AS start2, i2.stop AS stop2, i2.mapping AS mapping2,
            p1.id AS protein1_id, p1.accession AS accession1,
            p2.id AS protein2_id, p2.accession AS accession2
        FROM runs AS r,
            associations AS a,
            descriptions AS d,
            methods AS m,
            interactors AS i1, interactors AS i2,
            proteins AS p1, proteins AS p2
        WHERE r.id = a.run_id
            AND a.id = d.association_id
            AND m.id = d.method_id
            AND i1.id = d.interactor1_id
            AND i2.id = d.interactor2_id
            AND p1.id = i1.protein_id
            AND p2.id = i2.protein_id
            AND a.run_id = ?
            AND a.pmid = ?
        ORDER BY d.created_at DESC, d.id DESC
        LIMIT ? OFFSET ?
SQL;

    const COUNT_DESCRIPTIONS_SQL = <<<SQL
        SELECT COUNT(*)
        FROM associations AS a, descriptions AS d
        WHERE a.id = d.association_id
            AND a.run_id = ?
            AND a.pmid = ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function id(int $run_id, int $pmid, int $id): array
    {
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);

        $select_description_sth->execute([$run_id, $pmid, $id]);

        if ($description = $select_description_sth->fetch()) {
            return $this->formatted($description);
        }

        throw new NotFoundException(
            vsprintf('%s has no entry with run_id %s, pmid %s, id %s', [
                self::class,
                $run_id,
                $pmid,
                $id,
            ])
        );
    }

    public function pagination(int $run_id, int $pmid, int $page = 1, int $limit = 20): ResultSetInterface
    {
        $offset = ($page - 1) * $limit;
        $total = $this->count($run_id, $pmid);

        if ($page < 1) {
            throw new UnderflowException;
        }

        if ($offset > 0 && $total <= $offset) {
            throw new OverflowException;
        }

        $select_descriptions_sth = $this->pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $descriptions = [];

        $select_descriptions_sth->execute([$run_id, $pmid, $limit, $offset]);

        while ($description = $select_descriptions_sth->fetch()) {
            $descriptions[] = $this->formatted($description);
        }

        return new Pagination(new ResultSet($descriptions), $total, $page, $limit);
    }

    public function maxPage(int $run_id, int $pmid, int $limit = 20): int
    {
        $total = $this->count($run_id, $pmid);

        return (int) ceil($total/$limit);
    }

    private function count(int $run_id, int $pmid): int
    {
        $count_descriptions_sth = $this->pdo->prepare(self::COUNT_DESCRIPTIONS_SQL);

        $count_descriptions_sth->execute([$run_id, $pmid]);

        return ($nb = $count_descriptions_sth->fetchColumn()) ? $nb : 0;
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

    private function formatted(array $description): array
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
                'name' => $description['method_name'],
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
}
