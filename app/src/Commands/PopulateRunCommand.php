<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Actions\PopulateRunInterface;
use App\Actions\PopulateRunResult as Result;

final class PopulateRunCommand extends Command
{
    protected static $defaultName = 'runs:populate';

    public function __construct(
        private PopulateRunInterface $action,
    ) {
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
            Result::SUCCESS => fn ($name) => $this->success($output, $name),
            Result::NOT_FOUND => fn () => $this->notfound($output, $id),
            Result::ALREADY_POPULATED => fn ($name) => $this->alreadyPopulated($output, $name),
            Result::FAILURE => fn ($name) => $this->failure($output, $name),
        ]);
    }

    private function command(string $cmd): Command
    {
        if ($application = $this->getApplication()) {
            return $application->find($cmd);
        }

        throw new \Exception('no application');
    }

    private function success(OutputInterface $output, string $name): int
    {
        $output->writeln(
            sprintf('<info>Metadata of curation run \'%s\' publications successfully updated.</info>', $name)
        );

        return 0;
    }

    private function notfound(OutputInterface $output, int $id): int
    {
        $output->writeln(
            sprintf('<error>No run with [\'id\' => %s]</error>', $id)
        );

        return 1;
    }

    private function alreadyPopulated(OutputInterface $output, string $name): int
    {
        $output->writeln(
            sprintf('<info>Metadata of curation run \'%s\' publications are already populated</info>', $name)
        );

        return 1;
    }

    private function failure(OutputInterface $output, string $name): int
    {
        $output->writeln(
            sprintf('<error>Failed to retrieve metadata of run \'%s\' publications</error>', $name)
        );

        return 1;
    }
}
