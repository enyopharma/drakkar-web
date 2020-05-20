<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * A factory producing the application container.
 *
 * @param string    $env
 * @param bool      $debug
 * @return Psr\Container\ContainerInterface
 */
return function (string $env, bool $debug): ContainerInterface {
    $defaults = [
        'app.env' => fn () => $env,
        'app.debug' => fn () => $debug,
    ];

    $files = (array) glob(__DIR__ . '/../factories/*.php');

    return new Quanta\Container(array_reduce($files, function ($factories, $file) {
        return array_merge($factories, require $file);
    }, $defaults));
};
