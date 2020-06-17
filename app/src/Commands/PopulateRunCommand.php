<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Actions\PopulateRunResult;
use App\Actions\PopulateRunInterface;

final class PopulateRunCommand extends Command
{
    protected static $defaultName = 'runs:populate';

    private PopulateRunInterface $action;

    public function __construct(PopulateRunInterface $action)
    {
        $this->action = $action;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Populate the metadata of the publications of a curation run')
            ->setHelp('Metadata are downloaded from pubmed')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the curation run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = (int) ((array) $input->getArgument('id'))[0];

        // get the populate publication command.
        $command = $this->command('publication:populate');

        // create the populate publication callable.
        $populate = function (int $pmid) use ($command, $output): bool {
            return $command->run(new ArrayInput(['pmid' => $pmid]), $output) === 0;
        };

        // execute the action and produce a response.
        return $this->action->populate($id, $populate)->match([
            PopulateRunResult::SUCCESS => fn ($name) => $this->successOutput($output, $name),
            PopulateRunResult::NOT_FOUND => fn () => $this->notfoundOutput($output, $id),
            PopulateRunResult::ALREADY_POPULATED => fn ($name) => $this->alreadyPopulatedOutput($output, $name),
            PopulateRunResult::FAILURE => fn ($name) => $this->failureOutput($output, $name),
        ]);
    }

    private function command(string $name): Command
    {
        if ($application = $this->getApplication()) {
            return $application->find($name);
        }

        throw new \Exception('no application');
    }

    private function successOutput(OutputInterface $output, array $run): int
    {
        $output->writeln(
            sprintf('<info>Metadata of curation run \'%s\' publications successfully updated.</info>', $run['name'])
        );

        return 0;
    }

    private function notfoundOutput(OutputInterface $output, int $id): int
    {
        $output->writeln(
            sprintf('<error>No run with [\'id\' => %s]</error>', $id)
        );

        return 1;
    }

    private function alreadyPopulatedOutput(OutputInterface $output, array $run): int
    {
        $output->writeln(
            sprintf('<info>Metadata of curation run \'%s\' publications are already populated</info>', $run['name'])
        );

        return 1;
    }

    private function failureOutput(OutputInterface $output, array $run): int
    {
        $output->writeln(
            sprintf('<error>Failed to retrieve metadata of run \'%s\' publications</error>', $run['name'])
        );

        return 1;
    }
}
