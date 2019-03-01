<?php

(function () {
    $slashtrace = new SlashTrace\SlashTrace;

    $slashtrace->addHandler(new SlashTrace\EventHandler\DebugHandler);

    $slashtrace->register();
})();

return 1;
