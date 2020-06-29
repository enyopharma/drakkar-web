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
    return Quanta\Container::files(__DIR__ . '/factories/*.php');
};
