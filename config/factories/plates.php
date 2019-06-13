<?php declare(strict_types=1);

use League\Plates\Engine;

use App\Http\Extensions\Plates\HelpersExtension;

return [
    'factories' => [
        HelpersExtension::class => function () {
            return new HelpersExtension;
        },
    ],

    'extensions' => [
        Engine::class => function ($container, Engine $engine) {
            $engine->addData([
                'pending' => App\Domain\Publication::PENDING,
                'selected' => App\Domain\Publication::SELECTED,
                'discarded' => App\Domain\Publication::DISCARDED,
                'curated' => App\Domain\Publication::CURATED,
            ]);

            return $engine;
        },
    ],
];
