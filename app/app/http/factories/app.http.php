<?php

declare(strict_types=1);

use App\Http\UrlGenerator;

return [
    UrlGenerator::class => function () {
        return new UrlGenerator(require __DIR__ . '/../config/urls.php');
    }
];
