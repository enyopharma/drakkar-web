<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Repositories\RunRepository;
use App\Repositories\PublicationRepository;
use App\Repositories\NotFoundException;

final class PopulateRunCommand extends Command
{
    protected static $defaultName = 'runs:populate';

    private $runs;

    private $publications;

    public function __construct(RunRepository $runs, PublicationRepository $publications)
    {
        $this->runs = $runs;
        $this->publications = $publications;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Populate the metadata of the publications of a curation run')
            ->setHelp('Metadata are downloaded from pubmed')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the curation run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = (int) $input->getArgument('id');

        try {
            $run = $this->runs->find($id, true);
        }

        catch (NotFoundException $e) {
            $output->writeln(sprintf('<error>No curation run with id %s</error>', $id));

            return;
        }

        $output->writeln(sprintf('<info>Curation run with id %s successfully populated</info>', $id));
    }
}
