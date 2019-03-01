<?php declare(strict_types=1);

use Psr\Container\ContainerInterface;

return function (ContainerInterface $container) {
    return [
        $container->get(App\Cli\Commands\ExampleCommand::class),
    ];
};
