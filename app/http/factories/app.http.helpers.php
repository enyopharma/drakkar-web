<?php

declare(strict_types=1);

use App\Http\Helpers\UrlHelper;

return [
    UrlHelper::class => function () {
        return new UrlHelper(require __DIR__ . '/../config/urls.php');
    }
];
