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
$config = (require $root . '/config/app.php')($root);
$factories = (require $root . '/config/factories.php')($config);
$container = (require $root . '/config/container.php')($factories);

/**
 * Run the cli application.
 */
$container->get(Symfony\Component\Console\Application::class)->run();
