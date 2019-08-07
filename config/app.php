<?php

declare(strict_types=1);

/**
 * Return the app config from the env.
 *
 * The server env gets completed with the .env file.
 *
 * @param string    $root
 * @param string    $env
 * @param bool      $debug
 * @return array
 */
return function (string $root, string $env, bool $debug) {
    (new Symfony\Component\Dotenv\Dotenv(false))->load($root . '/.env');

    $env = function (string $name, string $default) {
        $v = $_ENV[$name] ?? getenv($name);

        return $v === false ? $default : $v;
    };

    return [
        'app.root' => $root,
        'app.env' => $env,
        'app.debug' => $debug,
        'sso.host' => $env('SSO_HOST', 'localhost'),
        'db.hostname' => $env('DB_HOSTNAME', 'localhost'),
        'db.database' => $env('DB_DATABASE', 'database'),
        'db.username' => $env('DB_USERNAME', 'username'),
        'db.password' => $env('DB_PASSWORD', 'password'),
        'db.port' => (int) $env('DB_PORT', '5432'),
        'redis.scheme' => $env('REDIS_SCHEME', 'tcp'),
        'redis.host' => $env('REDIS_HOST', 'localhost'),
        'redis.port' => (int) $env('REDIS_PORT', '6379'),
    ];
};
