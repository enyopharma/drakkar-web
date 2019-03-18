<?php declare(strict_types=1);

namespace Enyo\Queue;

final class Job implements \JsonSerializable
{
    private $name;

    private $input;

    public function __construct(string $name, array $input)
    {
        $this->name = $name;
        $this->input = $input;
    }

    public function jsonSerialize()
    {
        return [
            $this->name,
            $this->input,
        ];
    }
}
