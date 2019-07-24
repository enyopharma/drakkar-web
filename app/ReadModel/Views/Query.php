<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

final class Query
{
    private $pdo;

    private $clauses;

    public static function instance(\PDO $pdo): self
    {
        return new self($pdo, [
            'select' => [],
            'from' => [],
            'where' => [],
            'filters' => [],
            'groupby' => [],
            'having' => [],
            'orderby' => [],
            'sliced' => false,
        ]);
    }

    private function __construct(\PDO $pdo, array $clauses = [])
    {
        $this->pdo = $pdo;
        $this->clauses = $clauses;
    }

    public function select(string ...$xs): self
    {
        return new self($this->pdo, array_merge_recursive($this->clauses, [
            'select' => $xs,
        ]));
    }

    public function from(string ...$xs): self
    {
        return new self($this->pdo, array_merge_recursive($this->clauses, [
            'from' => $xs,
        ]));
    }

    public function where(string ...$xs): self
    {
        return new self($this->pdo, array_merge_recursive($this->clauses, [
            'where' => $xs,
        ]));
    }

    public function filters(string ...$xs): self
    {
        return new self($this->pdo, array_merge_recursive($this->clauses, [
            'filters' => $xs,
        ]));
    }

    public function groupby(string ...$xs): self
    {
        return new self($this->pdo, array_merge_recursive($this->clauses, [
            'groupby' => $xs,
        ]));
    }

    public function having(string ...$xs): self
    {
        return new self($this->pdo, array_merge_recursive($this->clauses, [
            'having' => $xs,
        ]));
    }

    public function orderby(string ...$xs): self
    {
        return new self($this->pdo, array_merge_recursive($this->clauses, [
            'orderby' => $xs,
        ]));
    }

    public function sliced(bool $sliced = true): self
    {
        return new self($this->pdo, array_merge_recursive($this->clauses, [
            'sliced' => $sliced,
        ]));
    }

    public function prepare(): \PDOStatement
    {
        if (count($this->clauses['select']) == 0) {
            throw new \LogicException('No select clause');
        }

        if (count($this->clauses['from']) == 0) {
            throw new \LogicException('No from clause');
        }

        $sql = vsprintf('SELECT %s FROM %s', [
            implode(', ', $this->clauses['select']),
            implode(', ', $this->clauses['from']),
        ]);

        if (count($this->clauses['where']) > 0 || count($this->clauses['filters']) > 0) {
            $where = array_merge($this->clauses['where'], array_map(function ($filter) {
                return $filter . ' = ?';
            }, $this->clauses['filters']));

            $sql.= ' WHERE ' . implode(' AND ', $where);
        }

        if (count($this->clauses['groupby']) > 0) {
            $sql.= ' GROUP BY ' . implode(', ', $this->clauses['groupby']);
        }

        if (count($this->clauses['having']) > 0) {
            $sql.= ' HAVING ' . implode(', ', $this->clauses['having']);
        }

        if (count($this->clauses['orderby']) > 0) {
            $sql.= ' ORDER BY ' . implode(', ', $this->clauses['orderby']);
        }

        if ($this->clauses['sliced']) {
            $sql.= ' LIMIT ? OFFSET ?';
        }

        return $this->pdo->prepare($sql);
    }
}
