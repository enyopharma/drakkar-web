<?php declare(strict_types=1);

use Quanta\Container\Maps\FactoryMapInterface;

use Quanta\Collections\Whitelist;
use Quanta\Collections\Blacklist;
use Quanta\Collections\ClassCollection;
use Quanta\Collections\VendorDirectory;
use Quanta\Collections\FilteredCollection;

use Quanta\Container\ConfiguredFactoryMap;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Configuration\ParameterArray;
use Quanta\Container\Configuration\ServiceProviderDiscovery;
use Quanta\Container\Configuration\MergedConfigurationSource;
use Quanta\Container\Configuration\ServiceProviderCollection;
use Quanta\Container\Configuration\PhpFileConfigurationSource;

/**
 * Return an associative array of factories from given configuration array.
 *
 * @param array $app
 * @return callable[]
 */
return function (array $app): array {
    // factory used to convert parameters to value implementations.
    $factory = new ValueFactory(...$app['parsers']);

    // iterator for installed classes.
    $vendor = new FilteredCollection(
        new ClassCollection(
            new VendorDirectory($app['discovery']['path'])
        ),
        new Whitelist(...$app['discovery']['whitelist']),
        new Blacklist(...$app['discovery']['blacklist'])
    );

    // build a factory map.
    $map = new ConfiguredFactoryMap(
        new MergedConfigurationSource(
            new ParameterArray($factory, $app['parameters']),
            new ServiceProviderDiscovery($vendor),
            new ServiceProviderCollection(...$app['providers']),
            new PhpFileConfigurationSource($factory, ...$app['files'])
        )
    );

    // create the factory map.
    return $map->factories();
};
