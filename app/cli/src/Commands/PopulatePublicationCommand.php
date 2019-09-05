<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use Domain\Actions\PopulatePublication;

use App\Cli\Responders\PublicationResponder;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PopulatePublicationCommand extends Command
{
    protected static $defaultName = 'publications:populate';

    private $domain;

    private $responder;

    public function __construct(PopulatePublication $domain, PublicationResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Populate the metadata of a publication')
            ->setHelp('Metadata are downloaded from pubmed')
            ->addArgument('pmid', InputArgument::REQUIRED, 'The pmid of the publication.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input = [
            'pmid' => (int) ((array) $input->getArgument('pmid'))[0],
        ];

        $payload = ($this->domain)($input);

        return ($this->responder)($output, $payload);
    }
}
