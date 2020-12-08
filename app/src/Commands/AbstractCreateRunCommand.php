<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Actions\StoreRunInterface;
use App\Actions\StoreRunResult as Result;
use App\Assertions\RunType;

abstract class AbstractCreateRunCommand extends Command
{
    public function __construct(
        private StoreRunInterface $action,
        private string $type,
    ) {
        RunType::argument($type);

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(sprintf('Create a %s curation run', strtoupper($this->type)))
            ->setHelp('PMID associated to the curation run are read from STDIN')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the curation run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = ((array) $input->getArgument('name'))[0];

        try {
            $pmids = $this->pmidsFromStdin();
        }

        catch (\UnexpectedValueException $e) {
            return $this->invalidPmid($output, $e->getMessage());
        }

        return $this->action->store($this->type, $name, ...$pmids)->match([
            Result::SUCCESS => fn ($id) => $this->success($output, $id),
            Result::NO_PMID => fn () => $this->noPmid($output, ),
            Result::RUN_ALREADY_EXISTS => fn ($id, $name) => $this->runAlreadyExists($output, $id, $name),
            Result::ASSOCIATION_ALREADY_EXISTS => fn ($id, $name, $pmid) => $this->associationAlreadyExists($output, $id, $name, $pmid),
        ]);
    }

    private function success(OutputInterface $output, int $id): int
    {
        $output->writeln(
            sprintf('<info>Curation run created with [\'id\' => %s].</info>', $id)
        );

        return 0;
    }

    private function invalidPmid(OutputInterface $output, string $message): int
    {
        $output->writeln(sprintf('<error>%s</error>', $message));

        return 1;
    }

    private function noPmid(OutputInterface $output, ): int
    {
        $output->writeln('<error>At least one pmid is required.</error>');

        return 1;
    }

    private function runAlreadyExists(OutputInterface $output, int $id, string $name): int
    {
        $output->writeln(
            vsprintf('<error>Name \'%s\' already used by \'%s\' curation run %s</error>', [
                $name,
                $this->type,
                $id,
            ]),
        );

        return 1;
    }

    private function associationAlreadyExists(OutputInterface $output, int $run_id, string $run_name, int $pmid): int
    {
        $output->writeln(
            vsprintf('<error>Publication with PMID %s is already associated with \'%s\' curation run %s (\'%s\')</error>', [
                $pmid,
                $this->type,
                $run_id,
                $run_name,
            ])
        );

        return 1;
    }

    private function pmidsFromStdin(): array
    {
        $pmids = [];

        $stdin = fopen('php://stdin', 'r');

        try {
            while ($stdin && $line = fgets($stdin)) {
                $line = rtrim($line);

                if (empty($line)) continue;

                if (!preg_match('/^[0-9]+$/', $line)) {
                    throw new \UnexpectedValueException(
                        vsprintf('Value \'%s\' from stdin is not a valid PMID', [
                            strlen($line) > 10 ? substr($line, 0, 10) . '...' : $line,
                        ])
                    );
                }

                $pmids[(int) $line] = true;
            }
        }

        catch (\UnexpectedValueException $e) {
            throw $e;
        }

        finally {
            $stdin && fclose($stdin);
        }

        return array_keys($pmids);
    }
}
