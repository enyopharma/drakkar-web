<?php declare(strict_types=1);

namespace App\Cli\Commands;

use App\Domain\Run;
use App\Domain\InsertRun;

use Enyo\Cli\Responder;

final class CreateVHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:vh';

    public function __construct(InsertRun $insert, Responder $responder)
    {
        parent::__construct(Run::VH, $insert, $responder);
    }
}
