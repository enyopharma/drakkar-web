<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Actions\PopulatePublicationResult;
use App\Actions\PopulatePublicationInterface;

final class PopulatePublicationCommand extends Command
{
    protected static $defaultName = 'publications:populate';

    private PopulatePublicationInterface $action;

    public function __construct(PopulatePublicationInterface $action)
    {
        $this->action = $action;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Populate the metadata of a publication')
            ->setHelp('Metadata are downloaded from pubmed')
            ->addArgument('pmid', InputArgument::REQUIRED, 'The pmid of the publication.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pmid = (int) ((array) $input->getArgument('pmid'))[0];

        return $this->action->populate($pmid)->match([
            PopulatePublicationResult::SUCCESS => fn () => $this->successOutput($output, $pmid),
            PopulatePublicationResult::ALREADY_POPULATED => fn () => $this->alreadyPopulatedOutput($output, $pmid),
            PopulatePublicationResult::NOT_FOUND => fn () => $this->notFoundOutput($output, $pmid),
            PopulatePublicationResult::PARSING_ERROR => fn ($message) => $this->parsingErrorOutput($output, $pmid, $message),
        ]);
    }

    private function successOutput(OutputInterface $output, int $pmid): int
    {
        $output->writeln(
            sprintf('<info>Metadata of publication with [\'pmid\' => %s] successfully updated.</info>', $pmid)
        );

        return 0;
    }

    private function alreadyPopulatedOutput(OutputInterface $output, int $pmid): int
    {
        $output->writeln(
            sprintf('<info>Metadata of publication with [\'pmid\' => %s] already populated</info>', $pmid)
        );

        return 1;
    }

    private function notFoundOutput(OutputInterface $output, int $pmid): int
    {
        $output->writeln(
            sprintf('<error>No publication with [\'pmid\' => %s]</error>', $pmid)
        );

        return 1;
    }

    private function parsingErrorOutput(OutputInterface $output, int $pmid, string $message): int
    {
        $output->writeln(
            sprintf('<error>Failed to retrieve metadata of publication with [\'pmid\' => %s] - %s</error>', $pmid, $message)
        );

        return 1;
    }
}
