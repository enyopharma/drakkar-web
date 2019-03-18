<?php declare(strict_types=1);

namespace Enyo\Queue;

final class JobFailure implements JobResultInterface
{
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function success(): bool
    {
        return false;
    }

    public function message(): string
    {
        return $this->message;
    }
}
