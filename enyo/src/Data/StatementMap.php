<?php declare(strict_types=1);

namespace Enyo\Data;

final class StatementMap
{
    private $pdo;

    private $queries;

    private $stmts;

    public function __construct(\PDO $pdo, array $queries)
    {
        $this->pdo = $pdo;
        $this->queries = $queries;
        $this->stmts = [];
    }

    public function transaction(callable $transaction)
    {
        $this->pdo->beginTransaction();

        $result = $transaction($this);

        $this->pdo->commit();

        return $result;
    }

    public function stmt(string $name, int ...$ins): \PDOStatement
    {
        if (key_exists($name, $this->queries)) {
            $sql = vsprintf($this->queries[$name], array_map([$this, 'in'], $ins));

            $key = md5($sql);

            if (! key_exists($key, $this->stmts)) {
                return $this->stmts[$key] = $this->pdo->prepare($sql);
            }

            return $this->stmts[$key];
        }

        throw new \LogicException(
            sprintf('No query named \'%s\'', $name)
        );
    }

    public function inserted(string $name, array $input = []): int
    {
        $stmt = $this->stmt($name);

        if ($stmt->execute($input)) {
            return (int) $this->pdo->lastInsertId();
        }

        throw new \LogicException(
            sprintf('Execution of \'%s\' failed: %s %s %s', $name, ...$stmt->errorInfo())
        );
    }

    public function executed(string $name, array $input = [], int ...$ins): \PDOStatement
    {
        $stmt = $this->stmt($name, ...$ins);

        if ($stmt->execute($input)) {
            return $stmt;
        }

        throw new \LogicException(
            sprintf('Execution of \'%s\' failed: %s %s %s', $name, ...$stmt->errorInfo())
        );
    }

    private function in(int $number): string
    {
        return implode(', ', array_pad([], $number, '?'));
    }
}
