<?php

declare(strict_types=1);

namespace App\Cli\Commands;

final class CreateVHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:vh';

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo, \Domain\Run::VH);
    }
}
