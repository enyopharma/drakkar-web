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
    $application = new Application;

    $responder = new App\Cli\Responders\PopulatePublicationResponder;

    $application->add(new App\Cli\Commands\CreateHHRunCommand(
        $container->get(PDO::class)
    ));

    $application->add(new App\Cli\Commands\CreateVHRunCommand(
        $container->get(PDO::class)
    ));

    $application->add(new App\Cli\Commands\PopulateRunCommand(
        $container->get(PDO::class),
        $container->get(Domain\Actions\PopulatePublicationInterface::class),
        $responder
    ));

    $application->add(new App\Cli\Commands\PopulatePublicationCommand(
        $container->get(Domain\Actions\PopulatePublicationInterface::class),
        $responder
    ));

    return $application;
};
