<?php declare(strict_types=1);

use Quanta\Container\Configuration;
use Quanta\Container\ConfigurationEntry;
use Quanta\Container\ConfigurationSource;
use Quanta\Container\ConfiguredFactoryMap;
use Quanta\Container\MergedConfigurationSource;
use Quanta\Container\InteropConfigurationSource;
use Quanta\Container\PhpFileConfigurationSource;
use Quanta\Container\Maps\FactoryMap;
use Quanta\Container\Maps\CompiledFactoryMap;
use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Passes\DummyProcessingPass;
use Quanta\Container\Factories\Factory;

/**
 * Return a factory map from the given configuration array.
 *
 * @param array $app
 * @return \Quanta\Container\Maps\FactoryMapInterface
 */
return function (array $app): FactoryMapInterface {
    // create a value factory.
    $factory = new Quanta\Container\Values\ValueFactory(...$app['parsers']);

    // closure creating a parameter from a value.
    $parameter = function ($value) use ($factory) {
        return new Factory($factory($value));
    };

    // create the factory map.
    return new CompiledFactoryMap(
        new ConfiguredFactoryMap(
            new MergedConfigurationSource(...[
                new InteropConfigurationSource(...$app['providers']),
                new PhpFileConfigurationSource($factory, ...$app['project']['php']),
                new ConfigurationSource(
                    new ConfigurationEntry(
                        new Configuration(
                            new FactoryMap(array_map($parameter, $app['immutables'])),
                            new DummyProcessingPass
                        )
                    )
                ),
            ])
        ),
        $app['compilation']['cache'],
        $app['compilation']['path']
    );
};
