<?php

declare(strict_types=1);

use App\Routing\UrlGenerator;

return [
    UrlGenerator::class => fn () => new UrlGenerator(
        require __DIR__ . '/../app/urls.php',
    ),
];
