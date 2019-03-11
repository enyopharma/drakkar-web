<?php declare(strict_types=1);

/**
 * Return the application configuration.
 *
 * @param string $root
 * @return array
 */
return function (string $root): array {
    /**
     * Get the app env.
     *
     * @var bool|string
     */
    $env = getenv('APP_ENV');

    /**
     * Get the app debug mode.
     *
     * @var bool|string
     */
    $debug = getenv('APP_DEBUG');

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
            new Quanta\Container\Values\ReferenceParser,
            new Quanta\Container\Values\InterpolatedStringParser,
        ],

        /**
         * The immutable values to register in the container.
         *
         * @var array
         */
        'immutables' => [
            'app.env' => $env === false ? 'development' : $env,
            'app.debug' => $debug && (strtolower((string) $debug) === 'true' || $debug === '1'),
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
                sprintf('%s/enyo/config/provided/*.php', $root),
                sprintf('%s/enyo/config/default/*.php', $root),
                sprintf('%s/config/factories/*.php', $root),
                sprintf('%s/config/factories/%s/*.php', $root, $env),
            ], $debug ? [
                sprintf('%s/config/factories/debug/*.php', $root)
            ] : []),
        ],

        /**
         * The compilation options.
         *
         * @var array
         */
        'compilation' => [
            'cache' => $env == 'production',
            'path' => $root . '/storage/app/factories.php',
        ],
    ];
};
