<?php declare(strict_types=1);

namespace App\Cli\Commands;

use App\Repositories\Run;

use Enyo\Data\StatementMap;

final class CreateHHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:hh';

    public function __construct(\PDO $pdo, StatementMap $stmts)
    {
        parent::__construct(Run::HH, $pdo, $stmts);
    }
}
