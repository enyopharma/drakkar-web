<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseFactoryInterface;

use League\Plates\Engine;
use Zend\Expressive\Helper\UrlHelper;

use App\Http\Handlers\NotFoundRequestHandler;
use App\Http\Responders\HtmlResponder;
use App\Http\Responders\JsonResponder;
use App\Http\Responders\DatasetResponder;
use App\Http\Responders\PublicationResponder;
use App\Http\Responders\DescriptionResponder;

return [
    NotFoundRequestHandler::class => function ($container) {
        return new RunResponder(
            $container->get(ResponseFactoryInterface::class),
            $container->get(Engine::class)
        );
    },
];
