<?php

declare(strict_types=1);

namespace Cli\Commands;

final class CreateHHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:hh';

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo, 'hh');
    }
}