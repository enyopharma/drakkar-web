<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use Domain\Actions\CreateRun;

use App\Cli\Responders\RunResponder;

final class CreateVHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:vh';

    public function __construct(CreateRun $domain, RunResponder $responder)
    {
        parent::__construct(\Domain\Run::VH, $domain, $responder);
    }
}
