<?php declare(strict_types=1);

/**
 * Return a configured Psr-11 container.
 *
 * @param array $config
 * @return Psr\Container\ContainerInterface
 */
return function (array $config): Psr\Container\ContainerInterface {
    $factories = array_map(function ($value) {
        return function () use ($value) { return $value; };
    }, $config);

    $files = (array) glob(__DIR__ . '/config/factories/*.php');

    $factories = array_merge($factories, ...array_map(function ($file) {
        return require $file;
    }, $files));

    return new Quanta\Container($factories);
};
