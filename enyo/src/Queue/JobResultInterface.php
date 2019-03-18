<?php declare(strict_types=1);

namespace Enyo\Queue;

interface JobResultInterface
{
    public function success(): bool;

    public function message(): string;
}
