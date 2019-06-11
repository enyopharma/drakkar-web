<?php declare(strict_types=1);

use Symfony\Component\Console\Application;

return [
    'factories' => [
        Application::class => function () {
            return new Application;
        },
    ],
];
