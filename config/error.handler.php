<?php declare(strict_types=1);

/**
 * Allow to register a global error handler.
 *
 * @param string    $root
 * @param string    $env
 * @param bool      $debug
 * @return void
 */
return function (string $root, string $env, bool $debug) {
    $slashtrace = new SlashTrace\SlashTrace;

    if ($debug) {
        $slashtrace->addHandler(new SlashTrace\EventHandler\DebugHandler);
    }

    $slashtrace->register();
};
