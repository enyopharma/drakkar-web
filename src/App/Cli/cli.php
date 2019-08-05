<?php

declare(strict_types=1);

return function (Psr\Container\ContainerInterface $container) {
    return [
        new App\Cli\Commands\CreateHHRunCommand(
            $container->get(Domain\Actions\CreateRun::class),
            $container->get(App\Cli\Responders\CliResponder::class)
        ),
        new App\Cli\Commands\CreateVHRunCommand(
            $container->get(Domain\Actions\CreateRun::class),
            $container->get(App\Cli\Responders\CliResponder::class)
        ),
        new App\Cli\Commands\PopulateRunCommand(
            $container->get(Domain\Actions\PopulateRun::class),
            $container->get(App\Cli\Responders\CliResponder::class)
        ),
        new App\Cli\Commands\PopulatePublicationCommand(
            $container->get(Domain\Actions\PopulatePublication::class),
            $container->get(App\Cli\Responders\CliResponder::class)
        ),
    ];
};
