<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use Psr\Http\Server\RequestHandlerInterface;

use App\Http\Handlers\Dispatcher;
use App\Http\Handlers\InnerMostRequestHandler;

/**
 * A factory producing the application request handler.
 *
 * @param string    $env
 * @param bool      $debug
 * @return Psr\Http\Server\RequestHandlerInterface
 */
return function (string $env, bool $debug): RequestHandlerInterface {
    /**
     * Build the container.
     */
    $files = array_merge(
        glob(__DIR__ . '/../../infrastructure/factories/*.php'),
        glob(__DIR__ . '/../../domain/factories/*.php'),
        glob(__DIR__ . '/factories/*.php')
    );

    $container = new Quanta\Container(array_reduce($files, function ($factories, $file) {
        return array_merge($factories, require $file);
    }, []));

    /**
     * Run the boot scripts.
     */
    foreach (glob(__DIR__ . '/boot/*.php') as $boot) (require $boot)($container);

    /**
     * Get the middleware factories.
     */
    $factories = (require __DIR__ . '/config/middleware.php')($container);

    /**
     * Return the application.
     */
    return array_reduce(array_reverse($factories), function ($app, $factory) {
        return new Dispatcher($app, new App\Http\Middleware\LazyMiddleware($factory));
    }, new InnerMostRequestHandler);
};
