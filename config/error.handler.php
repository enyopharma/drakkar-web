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
    $handler = new SlashTrace\EventHandler\DebugHandler;

    if (! $debug) {
        $handler->setRenderer(new class implements SlashTrace\DebugRenderer\DebugRenderer {
            public function render(SlashTrace\Event $event)
            {
                http_response_code(500);
            }
        });
    }

    $slashtrace = new SlashTrace\SlashTrace;
    $slashtrace->addHandler($handler);
    $slashtrace->register();
};
