<?php declare(strict_types=1);

namespace App\ReadModel;

final class Repository implements RepositoryInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function projection(string $name, ...$xs): ProjectionInterface
    {
        return new $name($this->pdo, ...$xs);
    }
}
