<?php declare(strict_types=1);

use Psr\Container\ContainerInterface;

use Utils\Http\RouteCollector;

return function (RouteCollector $collector) {
    $collector->get('/', Http\Handlers\IndexHandler::class, 'index');
};
