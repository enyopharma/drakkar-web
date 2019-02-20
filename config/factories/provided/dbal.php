<?php declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

return [
    'factories' => [
        Configuration::class => function () {
            return new Configuration;
        },

        Connection::class => function ($container) {
            $params = $container->get('dbal.parameters.default');
            $config = $container->get(Configuration::class);

            return DriverManager::getConnection($params, $config);
        },
    ],
];
