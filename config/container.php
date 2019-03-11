<?php declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * Return a Psr-11 container from the given associative array of factories.
 *
 * @param callable[]
 * @return \Psr\Container\ContainerInterface
 */
return function (array $factories): ContainerInterface {
    return new Quanta\Container($factories);
};
