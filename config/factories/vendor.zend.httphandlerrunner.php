<?php

declare(strict_types=1);

use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;

return [
    EmitterInterface::class => function ($container) {
        return $container->get(EmitterStack::class);
    },

    EmitterStack::class => function () {
        $stack = new EmitterStack;

        $stack->push(new Zend\HttpHandlerRunner\Emitter\SapiEmitter);
        $stack->push(new Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter);

        return $stack;
    },
];
