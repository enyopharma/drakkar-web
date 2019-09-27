<?php

namespace Domain\Services;

final class StableId
{
    private $i = 0;

    public function newStableId(): string
    {
        return 'EY' . strtoupper(bin2hex(random_bytes(4)));
    }
}
