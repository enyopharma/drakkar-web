<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class ProteinSql implements ProteinInterface
{
    private $pdo;

    private $id;

    private $type;

    private $accession;

    private $data;

    const SELECT_ISOFORMS_SQL = <<<SQL
        SELECT accession, sequence, is_canonical
        FROM sequences
        WHERE protein_id = ?
        ORDER BY is_canonical DESC, accession ASC
SQL;

    const SELECT_CHAINS_SQL = <<<SQL
        SELECT f.key, f.description, f.start, f.stop
        FROM sequences AS s, features AS f
        WHERE s.id = f.sequence_id
        AND s.is_canonical IS TRUE
        AND s.protein_id = ?
        AND f.key = 'CHAIN'
        ORDER BY f.start ASC, f.stop ASC
SQL;

    const SELECT_DOMAINS_SQL = <<<SQL
        SELECT f.key, f.description, f.start, f.stop
        FROM sequences AS s, features AS f
        WHERE s.id = f.sequence_id
        AND s.is_canonical IS TRUE
        AND s.protein_id = ?
        AND f.key IN ('TOPO_DOM', 'TRANSMEM', 'INTRAMEM', 'DOMAIN', 'REGION', 'MOTIF')
        ORDER BY f.start ASC, f.stop ASC
SQL;

    const SELECT_MATURES_SQL = <<<SQL
        SELECT i.name, i.start, i.stop
        FROM descriptions AS d, interactors AS i
        WHERE (i.id = d.interactor1_id OR i.id = d.interactor2_id)
        AND d.deleted_at IS NULL
        AND i.protein_id = ?
        GROUP BY i.name, i.start, i.stop
SQL;

    public function __construct(\PDO $pdo, int $id, string $type, string $accession, array $data = [])
    {
        $this->pdo = $pdo;
        $this->id = $id;
        $this->type = $type;
        $this->accession = $accession;
        $this->data = $data;
    }

    public function data(): array
    {
        $data = [
            'type' => $this->type,
            'accession' => $this->accession,
        ];

        return $data + $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->data();
    }

    public function withIsoforms(): self
    {
        $select_isoforms_sth = $this->pdo->prepare(self::SELECT_ISOFORMS_SQL);

        $select_isoforms_sth->execute([$this->id]);

        return new self($this->pdo, $this->id, $this->type, $this->accession, $this->data + [
            'isoforms' => (array) $select_isoforms_sth->fetchAll(),
        ]);
    }

    public function withChains(): self
    {
        $select_chains_sth = $this->pdo->prepare(self::SELECT_CHAINS_SQL);

        $select_chains_sth->execute([$this->id]);

        return new self($this->pdo, $this->id, $this->type, $this->accession, $this->data + [
            'chains' => (array) $select_chains_sth->fetchAll(),
        ]);
    }

    public function withDomains(): self
    {
        $select_domains_sth = $this->pdo->prepare(self::SELECT_DOMAINS_SQL);

        $select_domains_sth->execute([$this->id]);

        return new self($this->pdo, $this->id, $this->type, $this->accession, $this->data + [
            'domains' => (array) $select_domains_sth->fetchAll(),
        ]);
    }

    public function withMatures(): self
    {
        $select_matures_sth = $this->pdo->prepare(self::SELECT_MATURES_SQL);

        $select_matures_sth->execute([$this->id]);

        return new self($this->pdo, $this->id, $this->type, $this->accession, $this->data + [
            'matures' => (array) $select_matures_sth->fetchAll(),
        ]);
    }
}
