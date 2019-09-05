<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use Domain\Actions\PopulateRun;

use App\Cli\Responders\RunResponder;
use App\Cli\Responders\PublicationResponder;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PopulateRunCommand extends Command
{
    protected static $defaultName = 'runs:populate';

    private $domain;

    private $responder;

    public function __construct(PopulateRun $domain, RunResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;

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
        $input = [
            'id' => (int) ((array) $input->getArgument('id'))[0],
        ];

        $responder = new PublicationResponder;

        $payload = ($this->domain)($input, function ($payload) use ($responder, $output) {
            return $responder($output, $payload);
        });

        return ($this->responder)($output, $payload);
    }
}
