<?php

declare(strict_types=1);

/**
 * Return a factory producing the app request handler.
 *
 * Can be used in many context.
 *
 * @param string    $root
 * @param string    $env
 * @param bool      $debug
 * @return Psr\Http\Server\RequestHandlerInterface
 */
return function (string $root, string $env, bool $debug) {
    $config = (require $root . '/config/app.php')($root, $env, $debug);

    $container = (require $root . '/container.php')($config);

    return $container->get(Psr\Http\Server\RequestHandlerInterface::class);
};
