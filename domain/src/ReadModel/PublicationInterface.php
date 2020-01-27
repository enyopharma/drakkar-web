<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface PublicationInterface extends EntityInterface
{
    public function descriptions(): DescriptionViewInterface;
}
