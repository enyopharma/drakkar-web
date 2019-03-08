<?php declare(strict_types=1);

namespace App\Cli\Commands;

use App\Repositories\Run;
use App\Repositories\RunRepository;

final class CreateHHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:hh';

    public function __construct(RunRepository $runs)
    {
        parent::__construct(Run::HH, $runs);
    }
}
