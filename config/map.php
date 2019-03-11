<?php declare(strict_types=1);

use Quanta\Container\Configuration;
use Quanta\Container\ConfigurationEntry;
use Quanta\Container\ConfigurationSource;
use Quanta\Container\ConfiguredFactoryMap;
use Quanta\Container\MergedConfigurationSource;
use Quanta\Container\PhpFileConfigurationSource;
use Quanta\Container\Maps\FactoryMap;
use Quanta\Container\Maps\MergedFactoryMap;
use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Passes\ExtensionPass;
use Quanta\Container\Passes\MergedProcessingPass;
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

    // closure creating an extension pass from an id and an extension.
    $extension = function (string $id, callable $extension) {
        return new ExtensionPass($id, $extension);
    };

    // closure creating a configuration source from a service provider.
    $source = function ($provider) {
        $factories = $provider->getFactories();
        $extensions = $provider->getExtensions();

        foreach ($extensions as $id => $extension) {
            $passes[] = new ExtensionPass($id, $extension);
        }

        return new ConfigurationSource(
            new ConfigurationEntry(
                new Configuration(
                    new FactoryMap($factories),
                    new MergedProcessingPass(...($passes ?? []))
                )
            )
        );
    };

    // merge the map with the immutable parameters.
    return new MergedFactoryMap(
        new ConfiguredFactoryMap(
            new MergedConfigurationSource(...array_merge(
                array_map($source, $app['providers']), [
                new PhpFileConfigurationSource($factory, ...$app['project']['php'])
            ]))
        ),
        new FactoryMap(array_map($parameter, $app['immutables']))
    );
};
