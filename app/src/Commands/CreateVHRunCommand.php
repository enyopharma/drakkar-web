<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\StoreRunInterface;

final class CreateVHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:vh';

    public function __construct(StoreRunInterface $action)
    {
        parent::__construct($action, 'vh');
    }
}
