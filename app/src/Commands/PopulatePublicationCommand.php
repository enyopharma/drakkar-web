<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Actions\PopulatePublicationInterface;
use App\Actions\PopulatePublicationResult as Result;

final class PopulatePublicationCommand extends Command
{
    protected static $defaultName = 'publications:populate';

    private PopulatePublicationInterface $action;

    private ?OutputInterface $output = null;

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
        $this->output = $output;

        $pmid = (int) ((array) $input->getArgument('pmid'))[0];

        return $this->action->populate($pmid)->match([
            Result::SUCCESS => fn () => $this->success($pmid),
            Result::ALREADY_POPULATED => fn () => $this->alreadyPopulated($pmid),
            Result::NOT_FOUND => fn () => $this->notFound($pmid),
            Result::PARSING_ERROR => fn ($message) => $this->parsingError($pmid, $message),
        ]);
    }

    private function success(int $pmid): int
    {
        $this->output && $this->output->writeln(
            sprintf('<info>Metadata of publication with [\'pmid\' => %s] successfully updated.</info>', $pmid)
        );

        return 0;
    }

    private function alreadyPopulated(int $pmid): int
    {
        $this->output && $this->output->writeln(
            sprintf('<info>Metadata of publication with [\'pmid\' => %s] already populated</info>', $pmid)
        );

        return 1;
    }

    private function notFound(int $pmid): int
    {
        $this->output && $this->output->writeln(
            sprintf('<error>No publication with [\'pmid\' => %s]</error>', $pmid)
        );

        return 1;
    }

    private function parsingError(int $pmid, string $message): int
    {
        $this->output && $this->output->writeln(
            sprintf('<error>Failed to retrieve metadata of publication with [\'pmid\' => %s] - %s</error>', $pmid, $message)
        );

        return 1;
    }
}
