<?php declare(strict_types=1);

namespace App\ReadModel;

final class DatasetProjection implements ProjectionInterface
{
    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT a.pmid, m.psimi_id,
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
            AND a.state = 'curated'
            AND r.deleted_at IS NULL
            AND d.deleted_at IS NULL
        ORDER BY d.created_at DESC, d.id DESC
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function rset(array $criteria = []): ResultSetInterface
    {
        $select_descriptions_sth = $this->pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute();

        return new MappedResultSet(
            new PdoResultSet($select_descriptions_sth),
            new DatasetMapper
        );
    }
}
