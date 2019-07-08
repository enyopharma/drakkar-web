<?php declare(strict_types=1);

use Psr\Http\Server\RequestHandlerInterface;

use Quanta\Http\FIFODispatcher;

return [
    RequestHandlerInterface::class => function ($container) {
        return $container->get(FIFODispatcher::class);
    },

    FIFODispatcher::class => function ($container) {
        $factory = require sprintf('%s/app/http.php', $container->get('app.root'));

        $middleware = $factory(new Enyo\Http\Middleware\MiddlewareFactory($container));

        return new FIFODispatcher(
            new Enyo\Http\Handlers\InnerMostRequestHandler,
            ...$middleware
        );
    },
];
