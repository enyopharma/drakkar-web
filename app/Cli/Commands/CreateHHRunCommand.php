<?php declare(strict_types=1);

namespace App\Cli\Commands;

use App\Domain\Run;
use App\Domain\InsertRun;

final class CreateHHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:hh';

    public function __construct(InsertRun $insert)
    {
        parent::__construct(Run::HH, $insert);
    }
}
