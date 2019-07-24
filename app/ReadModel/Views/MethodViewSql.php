<?php

declare(strict_types=1);

namespace App\ReadModel\Views;

final class MethodViewSql implements MethodViewInterface
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function selectMethodsQuery(): Query
    {
        return Query::instance($this->pdo)
            ->select('psimi_id, name')
            ->from('methods');
    }

    public function psimiId(string $psimi_id)
    {
        $select_method_sth = $this->selectMethodsQuery()
            ->where('psimi_id = ?')
            ->prepare();

        $select_method_sth->execute([$psimi_id]);

        return ($method = $select_method_sth->fetch()) ? $method : false;
    }

    public function search(string $q, int $limit): array
    {
        $qs = array_map(function ($q) { return '%' . $q . '%'; }, array_filter(explode(' ', $q)));

        $select_methods_sth = $this->selectMethodsQuery()
            ->where(...array_pad([], count($qs), 'search ILIKE ?'))
            ->sliced()
            ->prepare();

        $select_methods_sth->execute(array_merge($qs, [$limit, 0]));

        return (array) $select_methods_sth->fetchAll();
    }
}
