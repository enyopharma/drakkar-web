<?php declare(strict_types=1);

use App\Services\Efetch;

return [
    Efetch::class => function () {
        return new Efetch;
    },
];