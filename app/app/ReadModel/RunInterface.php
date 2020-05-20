<?php

declare(strict_types=1);

namespace App\ReadModel;

interface RunInterface extends EntityInterface
{
    public function withNbPublications(): self;

    public function publications(): AssociationViewInterface;
}
