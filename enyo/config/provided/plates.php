<?php declare(strict_types=1);

use League\Plates\Engine;
use App\Extensions\Plates\Assets;

return [
    'factories' => [
        Engine::class => function ($container) {
            $xs[] = $container->get('plates.templates.path');

            if ($container->has('plates.templates.extension')) {
                $xs[] = $container->get('plates.templates.extension');
            }

            return new Engine(...$xs);
        },
    ],
];
