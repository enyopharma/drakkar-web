<?php

declare(strict_types=1);

/**
 * Get the container.
 */
$container = (require __DIR__ . '/container.php')('cli', true);

/**
 * Build the application and return it.
 */
$application = new Symfony\Component\Console\Application;;

$application->add(new App\Commands\CreateHHRunCommand(
    $container->get(App\Actions\StoreRunInterface::class),
));

$application->add(new App\Commands\CreateVHRunCommand(
    $container->get(App\Actions\StoreRunInterface::class),
));

$application->add(new App\Commands\PopulateRunCommand(
    $container->get(App\Actions\PopulateRunInterface::class),
));

$application->add(new App\Commands\PopulatePublicationCommand(
    $container->get(App\Actions\PopulatePublicationInterface::class),
));

return $application;
