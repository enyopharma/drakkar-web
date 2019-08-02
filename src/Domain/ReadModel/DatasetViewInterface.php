<?php

declare(strict_types=1);

namespace Domain\ReadModel;

interface DatasetViewInterface
{
    public function all(): Statement;
}
