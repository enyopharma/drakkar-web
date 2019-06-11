<?php declare(strict_types=1);

use App\Domain;
use App\Cli\Commands;
use App\Cli\Responders;

return function ($container, $app) {
    $responder = new Responders\Responder;

    $app->add(
        new Commands\CreateHHRunCommand(
            $container->get(Domain\InsertRun::class),
            $responder
        )
    );

    $app->add(
        new Commands\CreateVHRunCommand(
            $container->get(Domain\InsertRun::class),
            $responder
        )
    );

    $app->add(
        new Commands\PopulateRunCommand(
            $container->get(Domain\PopulateRun::class),
            new Responders\PopulateResponder($responder)
        )
    );

    $app->add(
        new Commands\PopulatePublicationCommand(
            $container->get(Domain\PopulatePublication::class),
            new Responders\PopulateResponder($responder)
        )
    );
};
