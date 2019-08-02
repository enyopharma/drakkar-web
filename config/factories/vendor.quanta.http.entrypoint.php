<?php

declare(strict_types=1);

use Quanta\Http\Entrypoint;

return [
    Entrypoint::class => function ($container) {
        /** @var Nyholm\Psr7Server\ServerRequestCreator */
        $creator = $container->get(Nyholm\Psr7Server\ServerRequestCreator::class);

        /** @var Zend\HttpHandlerRunner\Emitter\EmitterInterface */
        $emitter = $container->get(Zend\HttpHandlerRunner\Emitter\EmitterInterface::class);

        return new Entrypoint(
            [$creator, 'fromGlobals'],
            $container->get(Psr\Http\Server\RequestHandlerInterface::class),
            [$emitter, 'emit']
        );
    }
];
