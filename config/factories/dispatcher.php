<?php declare(strict_types=1);

return [
    'parameters' => [
        'http.middleware.queue.path' => '%{app.root}/config/middleware.php',
    ],

    'factories' => [
        'http.middleware.queue' => function ($container) {
            $factory = require $container->get('http.middleware.queue.path');

            return $factory($container);
        },
    ],
];
