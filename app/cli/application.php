<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use Symfony\Component\Console\Application;

/**
 * A factory producing the symfony cli application.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return Symfony\Component\Console\Application
 */
return function (ContainerInterface $container): Application {
    return new Application;
};
