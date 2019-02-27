<?php declare(strict_types=1);

/**
 * Return the application configuration.
 *
 * @param string $env
 * @param bool $debug
 * @return array
 */
return function (string $env, bool $debug): array {
    /**
     * The root of the application.
     *
     * @var string
     */
    $root = realpath(__DIR__ . '/..');

    /**
     * The configuration values.
     *
     * @var array
     */
    return [
        /**
         * The parser used to parse container values.
         *
         * @var \Quanta\Container\Values\ValueParserInterface[]
         */
        'parsers' => [
            new Quanta\Container\Values\EnvVarParser,
            new Quanta\Container\Values\InstanceParser,
            new Quanta\Container\Values\ReferenceParser,
            new Quanta\Container\Values\InterpolatedStringParser,
        ],

        /**
         * The immutable values to register in the container.
         *
         * @var array
         */
        'immutables' => [
            'app.env' => $env,
            'app.debug' => $debug,
            'app.root' => $root,
        ],

        /**
         * The service providers to import.
         *
         * @var \Interop\Container\ServiceProviderInterface[]
         */
        'providers' => [
            new Services\Http\NyholmHttpFactoryServiceProvider,
            new Services\Http\QuantaHttpEntrypointServiceProvider,
            new Services\Http\HttpDispatcherServiceProvider,
        ],

        /**
         * The glob paths of the project specific configuration files.
         *
         * @var array
         */
        'project' => [
            'php' => array_merge([
                sprintf('%s/shared/config/provided/*.php', $root),
                sprintf('%s/shared/config/default/*.php', $root),
                sprintf('%s/config/factories/*.php', $root),
                sprintf('%s/config/factories/%s/*.php', $root, $env),
            ], $debug ? [
                sprintf('%s/config/factories/debug/*.php', $root)
            ] : []),
        ],
    ];
};
