<?php declare(strict_types=1);

use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;

return [
    'aliases' => [
        EmitterInterface::class => EmitterStack::class,
    ],

    'factories' => [
        EmitterStack::class => function () {
            $stack = new EmitterStack;

            $stack->push(new Zend\HttpHandlerRunner\Emitter\SapiEmitter);

            return $stack;
        },
    ],
];
