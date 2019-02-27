<?php declare(strict_types=1);

return [
    'parameters' => [
        'session.handler.options' => [
            'ttl' => 'env(SESSION_TTL, 7200, int)',
            'prefix' => 'env(SESSION_PREFIX, session)',
        ],
    ],
];
