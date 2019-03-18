<?php declare(strict_types=1);

namespace Enyo\Queue;

final class DefaultJobHandler implements JobHandlerInterface
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __invoke(array $input): JobResultInterface
    {
        return new JobFailure(sprintf('No handler associated to job named %s', $this->name));
    }
}
