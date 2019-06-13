<?php declare(strict_types=1);

use Enyo\InstanceFactory;

return function (InstanceFactory $factory) {
    return [
        $factory(App\Cli\Commands\CreateHHRunCommand::class),
        $factory(App\Cli\Commands\CreateVHRunCommand::class),
        $factory(App\Cli\Commands\PopulateRunCommand::class),
        $factory(App\Cli\Commands\PopulatePublicationCommand::class),
    ];
};
