<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Domain\Actions\PopulatePublicationInterface;
use App\Cli\Responders\PopulatePublicationResponder;

final class PopulatePublicationCommand extends Command
{
    protected static $defaultName = 'publications:populate';

    private $action;

    private $responder;

    public function __construct(PopulatePublicationInterface $action, PopulatePublicationResponder $responder)
    {
        $this->action = $action;
        $this->responder = $responder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Populate the metadata of a publication')
            ->setHelp('Metadata are downloaded from pubmed')
            ->addArgument('pmid', InputArgument::REQUIRED, 'The pmid of the publication.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pmid = (int) ((array) $input->getArgument('pmid'))[0];

        $result = $this->action->populate($pmid);

        return $this->responder->write($output, $result);
    }
}
