<?php declare(strict_types=1);

/**
 * Get the root path.
 */
$root = (string) realpath(__DIR__ . '/../');

/**
 * Setup the autoloader.
 */
require $root . '/vendor/autoload.php';

/**
 * Load the env.
 */
(new Dotenv\Dotenv($root))->load();

/**
 * Register slashtrace as error handler.
 */
 $slashtrace = new SlashTrace\SlashTrace;

 $slashtrace->addHandler(new SlashTrace\EventHandler\DebugHandler);

 $slashtrace->register();

/**
 * Build the app container.
 */
 $app = (require $root . '/config/app.php')($root);
 $factories = (require $root . '/config/factories.php')($app);
 $container = (require $root . '/config/container.php')($factories);

/**
 * Call boot scripts with the container.
 */
(require $root . '/config/session.php')($container);

/**
 * Run the http application.
 */
$container->get(Quanta\HttpEntrypoint::class)->run();
