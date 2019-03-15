<?php declare(strict_types=1);

use Enyo\Cli\CommandFactory;

return function (CommandFactory $factory) {
    return [
        $factory(App\Cli\Commands\ExampleCommand::class),
        $factory(App\Cli\Commands\CreateHHRunCommand::class),
        $factory(App\Cli\Commands\CreateVHRunCommand::class),
        $factory(App\Cli\Commands\PopulatePublicationCommand::class),
    ];
};
