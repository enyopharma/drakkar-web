<?php declare(strict_types=1);

/**
 * Return a configured Psr-11 container.
 *
 * @return Psr\Container\ContainerInterface
 */
return (function (): Psr\Container\ContainerInterface {
    $config = array_map(function ($value) {
        return function () use ($value) { return $value; };
    }, require __DIR__ . '/config/app.php');

    $files = (array) glob(__DIR__ . '/config/factories/*.php');

    $factories = array_merge($config, ...array_map(function ($file) {
        return require $file;
    }, $files));

    return new Quanta\Container($factories);
})();
