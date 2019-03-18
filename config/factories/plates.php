<?php declare(strict_types=1);

use App\Http\Extensions\Plates\HelpersExtension;

return [
    'factories' => [
        HelpersExtension::class => function () {
            return new HelpersExtension;
        },
    ],
];
