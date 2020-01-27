<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface RunInterface extends EntityInterface
{
    public function withNbPublications(): self;

    public function publications(): AssociationViewInterface;
}
