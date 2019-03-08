<?php declare(strict_types=1);

namespace App\Cli\Commands;

use App\Repositories\Run;
use App\Repositories\RunRepository;

final class CreateVHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:vh';

    public function __construct(RunRepository $runs)
    {
        parent::__construct(Run::VH, $runs);
    }
}
