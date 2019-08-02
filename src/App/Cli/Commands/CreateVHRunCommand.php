<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use Domain\Actions\CreateRun;

use App\Cli\Responders\CliResponder;

final class CreateVHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:vh';

    public function __construct(CreateRun $domain, CliResponder $responder)
    {
        parent::__construct(\Domain\Run::VH, $domain, $responder);
    }
}
