<?php declare(strict_types=1);

use SlashTrace\SlashTrace;
use SlashTrace\EventHandler\DebugHandler;

/**
 * Get the root path.
 */
$root = (string) realpath(__DIR__ . '/../');

/**
 * Setup the autoloader.
 */
require $root . '/vendor/autoload.php';

/**
 * Load the env and build the container.
 */
(new Dotenv\Dotenv($root))->load();

$env = getenv('APP_ENV');
$debug = getenv('APP_DEBUG');

$env = $env === false ? 'development' : $env;
$debug = $debug && (strtolower($debug) === 'true' || $debug === '1');

/**
 * Register slashtrace as error handler.
 */
 $slashtrace = new SlashTrace;

 $slashtrace->addHandler(new DebugHandler);

 $slashtrace->register();

/**
 * Build the app container.
 */
$config = (require $root . '/config/app.php')($env, $debug);
$factories = (require $root . '/config/factories.php')($config);
$container = (require $root . '/config/container.php')($factories);

/**
 * Call boot script with the container.
 */
(require $root . '/config/boot.php')($container, $env, $debug);

/**
 * Run the http application.
 */
$container->get(Quanta\HttpEntrypoint::class)->run();
