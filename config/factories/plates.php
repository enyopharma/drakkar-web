<?php declare(strict_types=1);

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use App\Http\Extensions\Plates\HelpersExtension;

return [
    'factories' => [
        HelpersExtension::class => function () {
            return new HelpersExtension;
        },
    ],

    'extensions' => [
        Engine::class => function ($container, Engine $engine) {
            $xs[] = $container->get(HelpersExtension::class);

            foreach ($xs as $extension) {
                $engine->loadExtension($extension);
            }

            return $engine;
        },
    ],
];
