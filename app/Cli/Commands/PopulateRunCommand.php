<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Domain\PopulateRun;

use Enyo\Cli\Responder;

final class PopulateRunCommand extends Command
{
    protected static $defaultName = 'runs:populate';

    private $domain;

    private $responder;

    public function __construct(PopulateRun $domain, Responder $responder)
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
        $id = (int) $input->getArgument('id');

        return ($this->domain)($id)->parsed($this->bind('success', $id, $output), [
            PopulateRun::NOT_FOUND => $this->bind('notFound', $id, $output),
            PopulateRun::ALREADY_POPULATED => $this->bind('alreadyPopulated', $id, $output),
        ]);
    }

    private function bind(string $method, ...$xs)
    {
        return function ($data) use ($method, $xs) {
            return $this->{$method}(...array_merge($xs, [$data]));
        };
    }

    private function success(int $id, OutputInterface $output)
    {
        $this->responder->info($output, ...[
            'Metadata population jobs has been successfully fired for all publications of curation run with id %s.',
            $id,
        ]);
    }

    private function notFound(int $id, OutputInterface $output)
    {
        $this->responder->error($output, 'No curation run with id %s.', $id);
    }

    private function alreadyPopulated(int $id, OutputInterface $output)
    {
        $thsi->responder->info($output, 'All publications of curation run with id %s are already updated.', ...[
            $id,
        ]);
    }
}
