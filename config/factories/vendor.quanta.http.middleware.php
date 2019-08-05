<?php

declare(strict_types=1);

use Psr\Http\Server\RequestHandlerInterface;

use Quanta\Http\FIFODispatcher;

return [
    RequestHandlerInterface::class => function ($container) {
        return $container->get(FIFODispatcher::class);
    },

    FIFODispatcher::class => function ($container) {
        $factory = require sprintf('%s/src/App/Http/http.php', $container->get('app.root'));

        $middleware = $factory($container);

        return new FIFODispatcher(
            new App\Http\Handlers\InnerMostRequestHandler,
            ...$middleware
        );
    },
];
