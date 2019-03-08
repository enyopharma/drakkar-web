<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repositories\RunRepository;
use App\Repositories\NotUniqueException;

abstract class AbstractCreateRunCommand extends Command
{
    private $type;

    private $runs;

    public function __construct(string $type, RunRepository $runs)
    {
        $this->type = $type;
        $this->runs = $runs;

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

        // read pmids from stdin.
        try {
            $pmids = $this->pmids();
        }

        catch (\UnexpectedValueException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return;
        }

        // check if pmid are already associated to a HH/VH run.
        try {
            $run['id'] = $this->runs->insert($this->type, $name, ...$pmids);
        }

        catch (NotUniqueException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return;
        }

        // success message.
        $output->writeln(sprintf('<info>Curation run inserted with id %s</info>', $run['id']));
    }

    private function pmids(): array
    {
        $pmids = [];

        $stdin = fopen("php://stdin", "r");

        try {
            while ($line = fgets($stdin)) {
                $line = rtrim($line);

                if (empty($line)) continue;

                if (! preg_match('/^[0-9]+$/', $line)) {
                    throw new \UnexpectedValueException(
                        vsprintf('Value \'%s\' from stdin is not a valid PMID', [
                            strlen($line) > 10 ? substr($line, 0, 10) . '...' : $line,
                        ])
                    );
                }

                $pmids[$line] = true;
            }
        }

        catch (\UnexpectedValueException $e) {
            throw $e;
        }

        finally {
            fclose($stdin);
        }

        return array_keys($pmids);
    }
}
