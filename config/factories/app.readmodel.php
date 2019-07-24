<?php declare(strict_types=1);

use App\ReadModel\Psimi;
use App\ReadModel\Uniprot;
use App\ReadModel\Drakkar;
use App\ReadModel\PsimiInterface;
use App\ReadModel\UniprotInterface;
use App\ReadModel\DrakkarInterface;
use App\ReadModel\Views\RepositorySql;
use App\ReadModel\Views\RepositoryInterface;

return [
    RepositoryInterface::class => function ($container) {
        return $container->get(RepositorySql::class);
    },

    RepositorySql::class => function ($container) {
        return new RepositorySql(
            $container->get(PDO::class)
        );
    },

    PsimiInterface::class => function ($container) {
        return new Psimi(
            $container->get(RepositoryInterface::class)
        );
    },

    UniprotInterface::class => function ($container) {
        return new Uniprot(
            $container->get(RepositoryInterface::class)
        );
    },

    DrakkarInterface::class => function ($container) {
        return new Drakkar(
            $container->get(RepositoryInterface::class)
        );
    },
];
