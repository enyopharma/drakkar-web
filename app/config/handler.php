<?php

declare(strict_types=1);

/**
 * Get the app debug state and env.
 */
$env = $_ENV['APP_ENV'];
$debug = $_ENV['APP_DEBUG'];

/**
 * Get the container.
 */
$container = (require __DIR__ . '/container.php')($env, $debug);

/**
 * Run the boot scripts.
 */
foreach ((array) glob(__DIR__ . '/../boot/*.php') as $boot) {
    (require $boot)($container);
}

/**
 * Get the response factory from the container.
 */
$factory = $container->get(Psr\Http\Message\ResponseFactoryInterface::class);

/**
 * App Shutdown.
 */
if (file_exists(__DIR__ . '/shutdown')) {
    return Quanta\Http\Dispatcher::queue(new Middlewares\Shutdown($factory));
}

/**
 * Build a router from the Fast Route collector.
 */
$router = new Quanta\Http\FastRouteRouter(
    new FastRoute\Dispatcher\GroupCountBased(
        $container->get(FastRoute\RouteCollector::class)->getData(),
    )
);

return Quanta\Http\Dispatcher::queue(
    /**
     * Whoops error handler.
     */
    new Middlewares\Whoops(null, $factory),

    /**
     *  Not found html body.
     */
    new App\Middleware\NotFoundHtmlBodyMiddleware(
        $container->get(League\Plates\Engine::class)
    ),

    /**
     *  Not found json body.
     */
    new App\Middleware\NotFoundJsonBodyMiddleware,

    /**
     * Override the post method
     */
    (new Middlewares\MethodOverride($factory))->parsedBodyParameter('_method'),

    /**
     * Router.
     */
    new Quanta\Http\RoutingMiddleware($router),

    /**
     * Return a not allowed response.
     */
    new Quanta\Http\NotAllowedMiddleware($factory),

    /**
     * Return a not found response.
     */
    new Quanta\Http\NotFoundMiddleware($factory),

    /**
     * Parse json payload.
     */
    new Middlewares\JsonPayload,

    /**
     * Actually handle the request with the matched handler.
     */
    new Quanta\Http\RequestHandlerMiddleware,
);
