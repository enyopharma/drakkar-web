<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface ProteinInterface extends EntityInterface
{
    public function withIsoforms(): self;

    public function withChains(): self;

    public function withDomains(): self;

    public function withMatures(): self;
}
