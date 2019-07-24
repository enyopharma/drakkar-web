<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

final class RepositorySql implements RepositoryInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function methods(): MethodViewInterface
    {
        return new MethodViewSql($this->pdo);
    }

    public function proteins(): ProteinViewInterface
    {
        return new ProteinViewSql($this->pdo);
    }

    public function runs(): RunViewInterface
    {
        return new RunViewSql($this->pdo);
    }

    public function publications(): PublicationViewInterface
    {
        return new PublicationViewSql($this->pdo);
    }

    public function descriptions(): DescriptionViewInterface
    {
        return new DescriptionViewSql($this->pdo);
    }

    public function dataset(): DatasetViewInterface
    {
        return new DatasetViewSql($this->pdo);
    }
}
