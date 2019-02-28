<?php declare(strict_types=1);

namespace Shared\Sql;

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

    public function stmt(string $name, string ...$xs): \PDOStatement
    {
        if (key_exists($name, $this->queries)) {
            $sql = vsprintf($this->queries[$name], $xs);

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

    public function executed(string $name, array $input = [], string ...$xs): \PDOStatement
    {
        $stmt = $this->stmt($name, ...$xs);

        if ($stmt->execute($input)) {
            return $stmt;
        }

        throw new \LogicException(
            sprintf('Execution of \'%s\' failed', $name)
        );
    }
}
