<?php

declare(strict_types=1);

namespace App\ReadModel;

interface PublicationInterface extends EntityInterface
{
    public function descriptions(): DescriptionViewInterface;
}
