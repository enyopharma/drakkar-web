<?php declare(strict_types=1);

use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Parameter;
use Quanta\Container\Factories\Extension;

/**
 * Return an associative array of factories from the given configuration.
 *
 * @param array $config
 * @return callable[]
 */
return function (array $app): array {
    // create a value factory.
    $factory = new Quanta\Container\Values\ValueFactory(...$app['parsers']);

    // closure creating a parameter from a value.
    $parameter = function ($value) use ($factory) {
        return new Parameter($factory($value));
    };

    // closure creating an alias.
    $alias = function (string $id) {
        return new Alias($id);
    };

    // collect factories from service providers.
    $factories = array_map(function ($provider) {
        return $provider->getFactories();
    }, $app['providers']);

    // collect extensions from service providers.
    $extensions = array_map(function ($provider) {
        return $provider->getExtensions();
    }, $app['providers']);

    // collect factories from files.
    foreach ($app['project']['php'] as $pattern) {
        foreach (glob($pattern) as $path) {
            $configuration = require $path;

            $factories[] = array_map($parameter, $configuration['parameters'] ?? []);
            $factories[] = array_map($alias, $configuration['aliases'] ?? []);
            $factories[] = $configuration['factories'] ?? [];
            $extensions[] = $configuration['extensions'] ?? [];
        }
    }

    // create factories from immutable values.
    $factories[] = array_map($parameter, $app['immutables']);

    // merge the factories.
    $factories = array_merge([], ...$factories);

    // extend the factories.
    foreach ($extensions as $xs) {
        foreach ($xs as $id => $extension) {
            $factories[$id] = key_exists($id, $factories)
                ? new Extension($factories[$id], $extension)
                : $extension;
        }
    }

    // return the factories.
    return $factories;
};
