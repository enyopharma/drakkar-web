<?php declare(strict_types=1);

use Quanta\Http\Entrypoint;

return [
    'factories' => [
        Entrypoint::class => function ($container) {
            $creator = $container->get(Nyholm\Psr7Server\ServerRequestCreator::class);
            $handler = $container->get(Psr\Http\Server\RequestHandlerInterface::class);
            $emitter = $container->get(Zend\HttpHandlerRunner\Emitter\EmitterInterface::class);

            return new Entrypoint(
                [$creator, 'fromGlobals'],
                $handler,
                [$emitter, 'emit']
            );
        },
    ],
];
