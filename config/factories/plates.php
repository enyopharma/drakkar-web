<?php declare(strict_types=1);

use League\Plates\Engine;

use App\Domain\Publication;
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
                'pending' => Publication::PENDING,
                'selected' => Publication::SELECTED,
                'discarded' => Publication::DISCARDED,
                'curated' => Publication::CURATED,
            ]);

            return $engine;
        },
    ],
];
