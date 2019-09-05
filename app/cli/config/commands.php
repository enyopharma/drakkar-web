<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

/**
 * A factory returning the array of symfony cli application commands.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return Symfony\Component\Console\Command\Command[]
 */
return function (ContainerInterface $container): array {
    return [
        new App\Cli\Commands\CreateHHRunCommand(
            $container->get(Domain\Actions\CreateRun::class),
            $container->get(App\Cli\Responders\RunResponder::class)
        ),
        new App\Cli\Commands\CreateVHRunCommand(
            $container->get(Domain\Actions\CreateRun::class),
            $container->get(App\Cli\Responders\RunResponder::class)
        ),
        new App\Cli\Commands\PopulateRunCommand(
            $container->get(Domain\Actions\PopulateRun::class),
            $container->get(App\Cli\Responders\RunResponder::class)
        ),
        new App\Cli\Commands\PopulatePublicationCommand(
            $container->get(Domain\Actions\PopulatePublication::class),
            $container->get(App\Cli\Responders\PublicationResponder::class)
        ),
    ];
};
