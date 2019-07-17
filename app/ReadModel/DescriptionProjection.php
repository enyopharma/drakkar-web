<?php declare(strict_types=1);

namespace App\ReadModel;

final class DescriptionProjection implements ProjectionInterface
{
    const SELECT_DESCRIPTION_SQL = <<<SQL
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

    private $run_id;

    private $pmid;

    public function __construct(\PDO $pdo, int $run_id, int $pmid)
    {
        $this->pdo = $pdo;
        $this->run_id = $run_id;
        $this->pmid = $pmid;
    }

    public function rset(array $criteria = []): ResultSetInterface
    {
        return key_exists('id', $criteria)
            ? $this->id((int) $criteria['id'])
            : $this->pagination(
                (int) ($criteria['page'] ?? 1),
                (int) ($criteria['limit'] ?? 20)
            );
    }

    private function id(int $id): ResultSetInterface
    {
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);

        $select_description_sth->execute([$this->run_id, $this->pmid, $id]);

        return ($description = $select_description_sth->fetch())
            ? new MappedResultSet(
                new ArrayResultSet($description),
                new DescriptionMapper
            )
            : new EmptyResultSet(self::class, ['id' => $id]);
    }

    private function pagination(int $page, int $limit): ResultSetInterface
    {
        $offset = ($page - 1) * $limit;
        $total = $this->count();

        if ($page < 1 || ($offset > 0 && $total <= $offset)) {
            throw new \OutOfRangeException;
        }

        $select_descriptions_sth = $this->pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute([$this->run_id, $this->pmid, $limit, $offset]);

        return new Pagination(
            new MappedResultSet(
                new ArrayResultSet(...$select_descriptions_sth->fetchAll()),
                new DescriptionMapper
            ),
            $total,
            $page,
            $limit
        );
    }

    private function count(): int
    {
        $count_descriptions_sth = $this->pdo->prepare(self::COUNT_DESCRIPTIONS_SQL);

        $count_descriptions_sth->execute([$this->run_id, $this->pmid]);

        return ($nb = $count_descriptions_sth->fetchColumn()) ? $nb : 0;
    }
}
