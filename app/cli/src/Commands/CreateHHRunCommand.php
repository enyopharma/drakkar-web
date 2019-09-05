<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use Domain\Actions\CreateRun;

use App\Cli\Responders\RunResponder;

final class CreateHHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:hh';

    public function __construct(CreateRun $domain, RunResponder $responder)
    {
        parent::__construct(\Domain\Run::HH, $domain, $responder);
    }
}
