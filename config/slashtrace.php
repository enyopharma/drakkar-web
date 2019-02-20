<?php declare(strict_types=1);

use SlashTrace\SlashTrace;
use SlashTrace\EventHandler\DebugHandler;

return function (string $env, bool $debug) {
    $slashtrace = new SlashTrace;

    $slashtrace->addHandler(new DebugHandler);

    $slashtrace->register();
};
