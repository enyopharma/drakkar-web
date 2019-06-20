<?php declare(strict_types=1);

namespace App\ReadModel;

use App\Domain\Run;
use App\Domain\Protein;

use Enyo\ReadModel\ResultSet;
use Enyo\ReadModel\Pagination;
use Enyo\ReadModel\OverflowException;
use Enyo\ReadModel\UnderflowException;
use Enyo\ReadModel\ResultSetInterface;

final class DescriptionSumupProjection
{
    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT r.id AS run_id, r.type, a.pmid,
            d.id, d.created_at, d.deleted_at,
            m.psimi_id AS method_psimi_id, m.name AS method_name,
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

    const EAGER_LOAD_SELECT_ISOFORMS_SQL = <<<SQL
        SELECT i.protein_id AS protein_id, s.accession, s.sequence, s.is_canonical
        FROM descriptions AS d,
            interactors AS i,
            sequences AS s
        WHERE (i.id = d.interactor1_id OR i.id = d.interactor2_id)
            AND i.protein_id = s.protein_id
            AND d.id IN (
                SELECT d.id
                FROM associations AS a, descriptions AS d
                WHERE a.id = d.association_id
                    AND a.run_id = ?
                    AND a.pmid = ?
                ORDER BY d.created_at DESC, d.id DESC
                LIMIT ? OFFSET ?
            )

SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
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

        $isoforms = $this->isoforms($run_id, $pmid, $limit, $offset);

        while ($description = $select_descriptions_sth->fetch()) {
            $descriptions[] = [
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
                    'psimi_id' => $description['method_psimi_id'],
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
                    'mapping' => $this->mapping(
                        $this->widths(
                            $description['start1'],
                            $description['stop1'],
                            $isoforms[$description['protein1_id']]
                        ),
                        json_decode($description['mapping1'], true)
                    ),
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
                    'mapping' => $this->mapping(
                        $this->widths(
                            $description['start2'],
                            $description['stop2'],
                            $isoforms[$description['protein2_id']]
                        ),
                        json_decode($description['mapping2'], true)
                    ),
                ],
                'created_at' => $this->date($description['created_at']),
                'deleted_at' => $this->date($description['deleted_at']),
                'deleted' => ! is_null($description['deleted_at']),
            ];
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

    private function isoforms(int $run_id, int $pmid, int $limit = 20, int $offset = 0): array
    {
        $select_isoforms_sth = $this->pdo->prepare(self::EAGER_LOAD_SELECT_ISOFORMS_SQL);

        $isoforms = [];

        $select_isoforms_sth->execute([$run_id, $pmid, $limit, $offset]);

        while ($row = $select_isoforms_sth->fetch()) {
            $isoforms[$row['protein_id']][] = [
                'is_canonical' => $row['is_canonical'],
                'accession' => $row['accession'],
                'sequence' => $row['sequence'],
            ];
        }

        return $isoforms;
    }

    private function widths(int $start, int $stop, array $isoforms): array
    {
        $widths = [];

        foreach ($isoforms as $isoforms) {
            $widths[$isoforms['accession']] = $isoforms['is_canonical']
                ? $stop - $start + 1
                : $isoforms['stop'];
        }

        return $widths;
    }

    private function mapping(array $widths, array $alignments): array
    {
        $mapping = [];

        $maxwidth = max($widths);

        foreach ($alignments as $alignment) {
            foreach ($alignment['isoforms'] as $isoform) {
                foreach ($isoform['occurences'] as $occurence) {
                    $accession = $isoform['accession'];
                    $start = $occurence['start'];
                    $stop = $occurence['stop'];
                    $width = $stop - $start + 1;

                    if (! key_exists($accession, $mapping)) {
                        $mapping[$accession] = [
                            'accession' => $accession,
                            'start' => 1,
                            'stop' => $widths[$accession],
                            'width' => $widths[$accession],
                            'pstart' => 0,
                            'pstop' => $widths[$accession] * 100/$maxwidth,
                            'pwidth' => $widths[$accession] * 100/$maxwidth,
                            'maxwidth' => $maxwidth,
                            'occurences' => [],
                        ];
                    }

                    $mapping[$accession]['occurences'][] = [
                        'start' => $start,
                        'stop' => $stop,
                        'width' => $width,
                        'pstart' => ($start - 1) * 100/$maxwidth,
                        'pstop' => $stop * 100/$maxwidth,
                        'pwidth' => $width * 100/$maxwidth,
                        'maxwidth' => $maxwidth,
                    ];
                }
            }
        }

        return array_values($mapping);
    }

    private function date(?string $date): string
    {
        return is_null($date) ? '-' : date('Y - m - d', strtotime($date));
    }
}
