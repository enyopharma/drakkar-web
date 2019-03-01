<?php declare(strict_types=1);

use Enyo\Cli\CommandFactory;

return function (CommandFactory $factory) {
    return [
        $factory(App\Cli\Commands\ExampleCommand::class),
    ];
};
