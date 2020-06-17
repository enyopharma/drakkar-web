<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Actions\StoreRunResult;
use App\Actions\StoreRunInterface;

abstract class AbstractCreateRunCommand extends Command
{
    private StoreRunInterface $action;

    private string $type;

    public function __construct(StoreRunInterface $action, string $type)
    {
        if (!in_array($type, ['hh', 'vh'])) {
            throw new \InvalidArgumentException(
                sprintf('\'%s\' is not a valid curation run type.', $type)
            );
        }

        $this->action = $action;
        $this->type = $type;

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
        $type = $this->type;
        $name = ((array) $input->getArgument('name'))[0];

        try {
            $pmids = $this->pmidsFromStdin();
        }

        catch (\UnexpectedValueException $e) {
            return $this->invalidPmidOutput($output, $e->getMessage());
        }

        return $this->action->store($this->type, $name, ...$pmids)->match([
            StoreRunResult::SUCCESS => fn (...$xs) => $this->successOutput($output, ...$xs),
            StoreRunResult::NO_PMID => fn (...$xs) => $this->noPmidOutput($output, ...$xs),
            StoreRunResult::RUN_ALREADY_EXISTS => fn (int $id) => $this->runAlreadyExistsOutput($output, $id, $name),
            StoreRunResult::ASSOCIATION_ALREADY_EXISTS => fn (...$xs) => $this->associationAlreadyExistsOutput($output, ...$xs),
        ]);
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

    private function invalidPmidOutput(OutputInterface $output, string $message): int
    {
        $output->writeln(sprintf('<error>%s</error>', $message));

        return 1;
    }

    private function noPmidOutput(OutputInterface $output): int
    {
        $output->writeln('<error>At least one pmid is required.</error>');

        return 1;
    }

    private function runAlreadyExistsOutput(OutputInterface $output, int $id, string $name): int
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

    private function associationAlreadyExistsOutput(OutputInterface $output, int $run_id, string $run_name, int $pmid): int
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

    private function successOutput(OutputInterface $output, int $id): int
    {
        $output->writeln(
            sprintf('<info>Curation run created with [\'id\' => %s].</info>', $id)
        );

        return 0;
    }
}
