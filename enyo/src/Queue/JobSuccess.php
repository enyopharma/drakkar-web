<?php declare(strict_types=1);

namespace Enyo\Queue;

final class JobSuccess implements JobResultInterface
{
    public function success(): bool
    {
        return true;
    }

    public function message(): string
    {
        throw new \LogicException('The job was successful');
    }
}
