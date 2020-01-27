<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface EntityInterface extends \JsonSerializable
{
    public function data(): array;
}
