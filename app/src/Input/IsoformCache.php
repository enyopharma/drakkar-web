<?php

declare(strict_types=1);

namespace App\Input;

final class IsoformCache
{
    const SELECT_ISOFORM_SQL = <<<SQL
        SELECT s.sequence, s.is_canonical
        FROM proteins AS p, sequences AS s
        WHERE p.accession = s.canonical
        AND p.version = s.version
        AND p.id = ?
        AND s.accession = ?
    SQL;

    private \PDO $pdo;

    private int $protein_id;

    private int $start;

    private int $stop;

    private ?\PDOStatement $sth;

    private array $isoforms;

    public function __construct(\PDO $pdo, int $protein_id, int $start, int $stop)
    {
        $this->pdo = $pdo;
        $this->protein_id = $protein_id;
        $this->start = $start;
        $this->stop = $stop;
        $this->sth = null;
        $this->isoforms = [];
    }

    private function sth(): \PDOStatement
    {
        if (is_null($this->sth)) {
            $this->sth = $this->pdo->prepare(self::SELECT_ISOFORM_SQL);
        }

        return $this->sth;
    }

    /**
     * @return string|false
     */
    public function sequence(string $accession)
    {
        if (!key_exists($accession, $this->isoforms)) {
            $select_isoform_sth = $this->sth();

            $select_isoform_sth->execute([$this->protein_id, $accession]);

            $isoform = $select_isoform_sth->fetch();

            $this->isoforms[$accession] = $isoform['is_canonical']
                ? substr($isoform['sequence'], $this->start - 1, $this->stop - $this->start + 1)
                : $isoform['sequence'];
        }

        return $this->isoforms[$accession];
    }
}
