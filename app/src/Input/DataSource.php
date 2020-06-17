<?php

declare(strict_types=1);

namespace App\Input;

final class DataSource
{
    const SELECT_METHOD_SQL = <<<SQL
        SELECT * FROM methods WHERE psimi_id = ?
    SQL;

    const SELECT_PROTEIN_SQL = <<<SQL
        SELECT p.*, s.sequence
        FROM proteins AS p, sequences AS s
        WHERE p.id = s.protein_id
        AND s.is_canonical = TRUE
        AND p.accession = ?
    SQL;

    const SELECT_ISOFORM_SQL = <<<SQL
        SELECT p.accession AS protein, s.accession, s.sequence, s.is_canonical
        FROM proteins AS p, sequences AS s
        WHERE p.id = s.protein_id
        AND s.accession = ?
    SQL;

    const SELECT_INTERACTOR_NAME_SQL = <<<SQL
        SELECT i.name
        FROM descriptions AS d, interactors AS i, proteins AS p
        WHERE (i.id = d.interactor1_id OR i.id = d.interactor2_id)
        AND p.id = i.protein_id
        AND p.accession = ?
        AND i.start = ?
        AND i.stop = ?
        AND d.deleted_at IS NULL
        LIMIT 1
    SQL;

    const SELECT_INTERACTOR_COORDINATES_SQL = <<<SQL
        SELECT i.start, i.stop
        FROM descriptions AS d, interactors AS i, proteins AS p
        WHERE (i.id = d.interactor1_id OR i.id = d.interactor2_id)
        AND p.id = i.protein_id
        AND p.accession = ?
        AND i.name = ?
        AND d.deleted_at IS NULL
        LIMIT 1
    SQL;

    private \PDO $pdo;

    private array $sths;

    private array $methods;

    private array $proteins;

    private array $isoforms;

    private array $names;

    private array $coordinates;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->sths = [];
        $this->methods = [];
        $this->proteins = [];
        $this->isoforms = [];
        $this->names = [];
        $this->coordinates = [];
    }

    private function prepare(string $sql): \PDOStatement
    {
        $key = md5($sql);

        if (!key_exists($key, $this->sths)) {
            $this->sths[$key] = $this->pdo->prepare($sql);
        }

        return $this->sths[$key];
    }

    /**
     * @return mixed[]|false
     */
    public function method(string $psimi_id)
    {
        if (!key_exists($psimi_id, $this->methods)) {
            $select_method_sth = $this->prepare(self::SELECT_METHOD_SQL);

            $select_method_sth->execute([$psimi_id]);

            $this->methods[$psimi_id] = $select_method_sth->fetch();
        }

        return $this->methods[$psimi_id];
    }

    /**
     * @return mixed[]|false
     */
    public function protein(string $accession)
    {
        if (!key_exists($accession, $this->proteins)) {
            $select_protein_sth = $this->prepare(self::SELECT_PROTEIN_SQL);

            $select_protein_sth->execute([$accession]);

            $this->proteins[$accession] = $select_protein_sth->fetch();
        }

        return $this->proteins[$accession];
    }

    /**
     * @return mixed[]|false
     */
    public function isoform(string $accession)
    {
        if (!key_exists($accession, $this->isoforms)) {
            $select_isoform_sth = $this->prepare(self::SELECT_ISOFORM_SQL);

            $select_isoform_sth->execute([$accession]);

            $this->isoforms[$accession] = $select_isoform_sth->fetch();
        }

        return $this->isoforms[$accession];
    }

    /**
     * @return mixed[]|false
     */
    public function name(string $accession, int $start, int $stop)
    {
        $key = implode('.', [$accession, $start, $stop]);

        if (!key_exists($key, $this->names)) {
            $select_interactor_sth = $this->prepare(self::SELECT_INTERACTOR_NAME_SQL);

            $select_interactor_sth->execute([$accession, $start, $stop]);

            $this->names[$key] = $select_interactor_sth->fetch();
        }

        return $this->names[$key];
    }

    /**
     * @return mixed[]|false
     */
    public function coordinates(string $accession, string $name)
    {
        $key = implode('.', [$accession, $name]);

        if (!key_exists($key, $this->coordinates)) {
            $select_interactor_sth = $this->prepare(self::SELECT_INTERACTOR_COORDINATES_SQL);

            $select_interactor_sth->execute([$accession, $name]);

            $this->coordinates[$key] = $select_interactor_sth->fetch();
        }

        return $this->coordinates[$key];
    }
}
