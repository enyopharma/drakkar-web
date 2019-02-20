<?php declare(strict_types=1);

/**
 * Return a Psr-11 container from the given associative array of factories.
 *
 * @param callable[]
 * @return \Psr\Container\ContainerInterface
 */
return function (array $factories): Psr\Container\ContainerInterface {
    return new Quanta\Container($factories);
};
