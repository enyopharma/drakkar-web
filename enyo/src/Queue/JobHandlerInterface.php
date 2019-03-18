<?php declare(strict_types=1);

namespace Enyo\Queue;

interface JobHandlerInterface
{
    public function __invoke(array $input): JobResultInterface;
}
