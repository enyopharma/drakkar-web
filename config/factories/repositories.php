<?php declare(strict_types=1);

use App\Repositories\RunRepository;

return [
    'factories' => [
        RunRepository::class => function ($container) {
            $pdo = $container->get(\PDO::class);

            return new RunRepository($pdo);
        },
    ],
];
