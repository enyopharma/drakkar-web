<?php

declare(strict_types=1);

namespace Domain\ReadModel;

final class RunSql implements RunInterface
{
    private $pdo;

    private $id;

    private $type;

    private $name;

    private $data;

    const COUNT_PUBLICATIONS_SQL = <<<SQL
        SELECT state, COUNT(*)
        FROM associations
        WHERE run_id = ?
        GROUP BY state
SQL;

    public function __construct(\PDO $pdo, int $id, string $type, string $name, array $data = [])
    {
        $this->pdo = $pdo;
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->data = $data;
    }

    public function data(): array
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
        ];

        return $data + $this->data + [
            'url' => [
                'run_id' => $this->id,
            ],
        ];
    }

    public function withNbPublications(): self
    {
        $count_publications_sth = $this->pdo->prepare(self::COUNT_PUBLICATIONS_SQL);

        $count_publications_sth->execute([$this->id]);

        $nbs = (array) $count_publications_sth->fetchAll(\PDO::FETCH_KEY_PAIR);

        return new self($this->pdo, $this->id, $this->type, $this->name, array_merge($this->data, [
            'nbs' => [
                \Domain\Publication::PENDING => $nbs[\Domain\Publication::PENDING] ?? 0,
                \Domain\Publication::SELECTED => $nbs[\Domain\Publication::SELECTED] ?? 0,
                \Domain\Publication::DISCARDED => $nbs[\Domain\Publication::DISCARDED] ?? 0,
                \Domain\Publication::CURATED => $nbs[\Domain\Publication::CURATED] ?? 0,
            ],
        ]));
    }

    public function publications(): AssociationViewInterface
    {
        return new AssociationViewSql($this->pdo, $this->id, [
            'run' => $this->data(),
        ]);
    }
}
