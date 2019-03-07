<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Enyo\Data\StatementMap;

abstract class AbstractCreateRunCommand extends Command
{
    private $type;

    private $stmts;

    public function __construct(string $type, \Pdo $pdo, StatementMap $stmts)
    {
        $this->type = $type;
        $this->pdo = $pdo;
        $this->stmts = $stmts;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(sprintf('Create a %s curation run', strtoupper($this->type)))
            ->setHelp('PMID associated to the curation run are read from STDIN')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the curation run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        try {
            $pmids = $this->pmids();
        }

        catch (\UnexpectedValueException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }

        // check if pmid are already associated to a HH/VH run.

        $this->pdo->beginTransaction();

        $this->stmts->executed('runs/insert', [$this->type, $name]);

        $run_id = $this->pdo->lastInsertId();

        foreach ($pmids as $pmid) {
            $this->stmts->executed('publications/insert', [$pmid]);

            $publication_id =  $this->pdo->lastInsertId();

            $this->stmts->executed('associations/insert', [$run_id, $publication_id]);
        }

        $this->pdo->commit();
    }

    private function pmids(): array
    {
        $pmids = [];

        $stdin = fopen("php://stdin", "r");

        while ($line = fgets($stdin)) {
            $line = rtrim($line);

            if (empty($line)) continue;

            if (! preg_match('/^[0-9]+$/', $line)) {
                throw new \UnexpectedValueException(
                    sprintf('Value %s from stdin is not a pmid', strlen($line) > 10
                        ? substr($line, 0, 10) . '...'
                        : $line
                    )
                );
            }

            $pmids[$line] = true;
        }

        fclose($stdin);

        return array_keys($pmids);
    }
}
