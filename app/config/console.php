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

$efetch = new App\Services\Efetch;
$responder = new App\Commands\PopulatePublicationResponder;

$application->add(new App\Commands\CreateHHRunCommand($pdo));

$application->add(new App\Commands\CreateVHRunCommand($pdo));

$application->add(new App\Commands\PopulateRunCommand($pdo,
    new App\Actions\PopulatePublicationSql($pdo, $efetch),
    $responder,
));

$application->add(new App\Commands\PopulatePublicationCommand(
    new App\Actions\PopulatePublicationSql($pdo, $efetch),
    $responder,
));

return $application;
