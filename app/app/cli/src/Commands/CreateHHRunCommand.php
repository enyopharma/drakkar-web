<?php

declare(strict_types=1);

namespace App\Cli\Commands;

final class CreateHHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:hh';

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo, \Domain\Run::HH);
    }
}
