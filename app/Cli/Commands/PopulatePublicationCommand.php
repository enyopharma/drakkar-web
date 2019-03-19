<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Domain\PopulatePublication;

use Enyo\Cli\Responder;

final class PopulatePublicationCommand extends Command
{
    protected static $defaultName = 'publications:populate';

    private $domain;

    private $responder;

    public function __construct(PopulatePublication $domain, Responder $responder)
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
        $pmid = (int) $input->getArgument('pmid');

        return ($this->domain)($pmid)->parsed($this->bind('success', $pmid, $output), [
            PopulatePublication::NOT_FOUND => $this->bind('notFound', $pmid, $output),
            PopulatePublication::ALREADY_POPULATED => $this->bind('alreadyPopulated', $pmid, $output),
            PopulatePublication::QUERY_FAILED => $this->bind('queryFailed', $pmid, $output),
            PopulatePublication::PARSING_FAILED => $this->bind('parsingFailed', $pmid, $output),
        ]);
    }

    private function bind(string $method, ...$xs)
    {
        return function ($data) use ($method, $xs) {
            return $this->{$method}(...array_merge($xs, [$data]));
        };
    }

    private function success(int $pmid, OutputInterface $output)
    {
        $this->responder->info('Metadata of publication with pmid %s successfully updated.', ...[
            $output,
            $pmid,
        ]);
    }

    private function notFound(int $pmid, OutputInterface $output)
    {
        $this->responder->error($output, 'No publication with pmid %s.', $pmid);
    }

    private function alreadyPopulated(int $pmid, OutputInterface $output)
    {
        $this->responder->info('The metadata of publication with pmid %s are already updated.', ...[
            $output,
            $pmid,
        ]);
    }

    private function queryFailed(int $pmid, OutputInterface $output)
    {
        $this->responder->error('Pubmed query failed for publication %s.', ...[
            $output,
            $pmid,
        ]);
    }

    private function parsingFailed(int $pmid, OutputInterface $output, array $data)
    {
        $this->responder->error('Metadata parsing failed with code %s for publication with pmid %s.', ...[
            $output,
            $data['error'],
            $pmid,
        ]);

        $this->responder->default('Retrieved contents:', $output);
        $this->responder->default($data['contents'], $output);
    }
}
