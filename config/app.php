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
         * @var Quanta\Container\Values\ValueParserInterface[]
         */
        'parsers' => [
            new Quanta\Container\Values\EnvVarParser,
            new Quanta\Container\Values\ReferenceParser,
            new Quanta\Container\Values\InterpolatedStringParser,
        ],

        /**
         * The parameters to register in the container.
         *
         * @var array
         */
        'parameters' => [
            'app.env' => $env === false ? 'development' : $env,
            'app.debug' => $debug && (strtolower((string) $debug) === 'true' || $debug === '1'),
            'app.root' => $root,
        ],

        /**
         * The service provider autodiscovery.
         *
         * @var array
         */
        'discovery' => [
            'path' => $root . '/vendor',

            'whitelist' => [
                '/ServiceProvider/',
            ],

            'blacklist' => [
                //
            ]
        ],

        /**
         * The service providers to import.
         *
         * @var \Interop\Container\ServiceProviderInterface[]
         */
        'providers' => [
            //
        ],

        /**
         * The glob paths of the project specific configuration files.
         *
         * @var string[]
         */
        'files' => array_merge([
            sprintf('%s/enyo/config/*.php', $root),
            sprintf('%s/config/factories/*.php', $root),
            sprintf('%s/config/factories/%s/*.php', $root, $env),
        ], $debug ? [
            sprintf('%s/config/factories/debug/*.php', $root)
        ] : []),
    ];
};
