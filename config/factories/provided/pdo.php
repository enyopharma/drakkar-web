<?php declare(strict_types=1);

use Utils\Database\PDOCnxPool;

return [
    'factories' => [
        PDOCnxPool::class => function ($container) {
            $configurations = $container->get('pdo.configurations');

            return new PDOCnxPool($configurations);
        },

        \PDO::class => function ($container) {
            return $container->get(PDOCnxPool::class)->cnx('default');
        },
    ],
];
