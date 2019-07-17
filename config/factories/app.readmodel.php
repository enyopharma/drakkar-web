<?php declare(strict_types=1);

use App\ReadModel\Repository;
use App\ReadModel\RepositoryInterface;

return [
    RepositoryInterface::class => function ($container) {
        return $container->get(Repository::class);
    },

    Repository::class => function ($container) {
        return new Repository(
            $container->get(PDO::class)
        );
    },
];
