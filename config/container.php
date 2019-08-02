<?php declare(strict_types=1);

/**
 * Return a configured Psr-11 container.
 *
 * @param string    $root
 * @param string    $env
 * @param bool      $debug
 * @return Psr\Container\ContainerInterface
 */
return function (string $root, string $env, bool $debug): Psr\Container\ContainerInterface {
    $app = [
        'app.root' => function () use ($root) { return $root; },
        'app.env' => function () use ($env) { return $env; },
        'app.debug' => function () use ($debug) { return $debug; },
    ];

    $configurations = (array) glob($root . '/config/factories/*.php');

    $factories = array_merge($app, ...array_map(function ($file) {
        return require $file;
    }, $configurations));

    return new Quanta\Container($factories);
};
