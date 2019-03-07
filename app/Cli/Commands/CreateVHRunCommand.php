<?php declare(strict_types=1);

namespace App\Cli\Commands;

use App\Repositories\Run;

use Enyo\Data\StatementMap;

final class CreateVHRunCommand extends AbstractCreateRunCommand
{
    protected static $defaultName = 'runs:create:vh';

    public function __construct(\PDO $pdo, StatementMap $stmts)
    {
        parent::__construct(Run::VH, $pdo, $stmts);
    }
}
