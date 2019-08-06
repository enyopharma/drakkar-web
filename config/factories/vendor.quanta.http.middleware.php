<?php

declare(strict_types=1);

use Psr\Http\Server\RequestHandlerInterface;

use Quanta\Http\FIFODispatcher;

return [
    'quanta.http.middleware.queue' => function ($container) {
        $factory = require sprintf('%s/src/App/Http/http.php', $container->get('app.root'));

        return $factory($container);
    },

    RequestHandlerInterface::class => function ($container) {
        return $container->get(FIFODispatcher::class);
    },

    FIFODispatcher::class => function ($container) {
        return new FIFODispatcher(
            new App\Http\Handlers\InnerMostRequestHandler,
            ...$container->get('quanta.http.middleware.queue')
        );
    },
];
