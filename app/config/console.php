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

$pdo = $container->get(PDO::class);
$store_run_action = $container->get(App\Actions\StoreRunInterface::class);

$efetch = new App\Services\Efetch;
$responder = new App\Commands\PopulatePublicationResponder;

$application->add(new App\Commands\CreateHHRunCommand($store_run_action));

$application->add(new App\Commands\CreateVHRunCommand($store_run_action));

$application->add(new App\Commands\PopulateRunCommand($pdo,
    new App\Actions\PopulatePublicationSql($pdo, $efetch),
    $responder,
));

$application->add(new App\Commands\PopulatePublicationCommand(
    new App\Actions\PopulatePublicationSql($pdo, $efetch),
    $responder,
));

return $application;
