<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\StoreRunInterface;

final class CreateHHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:hh';

    public function __construct(StoreRunInterface $action)
    {
        parent::__construct($action, 'hh');
    }
}
