<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Domain\Services\PublicationMetadataService;
use App\Cli\Responders\PublicationMetadataResponder;

final class PopulatePublicationCommand extends Command
{
    protected static $defaultName = 'publications:populate';

    private $service;

    private $responder;

    public function __construct(PublicationMetadataService $service, PublicationMetadataResponder $responder)
    {
        $this->service = $service;
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

        $result = $this->service->populate($pmid);

        return $this->responder->write($output, $result);
    }
}
