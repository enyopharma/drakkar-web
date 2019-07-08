<?php declare(strict_types=1);

/**
 * Allow to register envvars and return app env and debug mode.
 *
 * @param string $root
 * @return array
 */
return function (string $root): array {
    (new Dotenv\Dotenv($root))->load();

    $env = getenv('APP_ENV');
    $debug = getenv('APP_DEBUG');

    $env = $env === false ? 'production' : $env;
    $debug = $debug && (strtolower((string) $debug) === 'true' || $debug === '1');

    return [$env, $debug];
};
