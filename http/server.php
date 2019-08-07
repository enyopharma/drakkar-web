<?php

declare(strict_types=1);

/**
 * Return an http server from the given app root, env name and debug mode.
 *
 * @param string    $root
 * @param string    $env
 * @param bool      $debug
 * @return App\Http\Server
 */
return function (string $root, string $env, bool $debug) {
    return new Quanta\Http\Server(function () use ($root, $env, $debug) {
        Quanta\Http\ExceptionHandler::register($debug, false);

        $config = (require $root . '/config/app.php')($root, $env, $debug);

        $container = (require $root . '/container.php')($config);

        $creator = $container->get(Nyholm\Psr7Server\ServerRequestCreator::class);
        $handler = $container->get(Psr\Http\Server\RequestHandlerInterface::class);

        $request = $creator->fromGlobals();

        return $handler->handle($request);
    });
};
