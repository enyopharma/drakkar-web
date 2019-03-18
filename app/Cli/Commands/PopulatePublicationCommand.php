<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Domain\PopulatePublication;

final class PopulatePublicationCommand extends Command
{
    protected static $defaultName = 'publications:populate';

    private $domain;

    public function __construct(PopulatePublication $domain)
    {
        $this->domain = $domain;

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
            PopulatePublication::QUERY_FAILED => $this->bind('queryFailed', $pmid, $output),
            PopulatePublication::PARSING_FAILED => $this->bind('parsingFailed', $pmid, $output),
            PopulatePublication::NOT_FOUND => $this->bind('notFound', $pmid, $output),
        ]);
    }

    private function bind(string $method, ...$xs)
    {
        return function ($data) use ($method, $xs) {
            return $this->{$method}(...array_merge($xs, [$data]));
        };
    }

    private function success(int $pmid, $output)
    {
        $output->writeln(
            sprintf('<info>Publication %s successfully populated</info>', $pmid)
        );
    }

    private function queryFailed(int $pmid, $output)
    {
        $output->writeln(
            sprintf('<error>Query failed for publication %s</error>', $pmid)
        );
    }

    private function parsingFailed($pmid, $output, array $data)
    {
        $output->writeln(
            vsprintf('<error>Metadata parsing failed with code %s for pmid %s</error>', [
                $data['error'],
                $pmid,
            ]
        ));

        $output->writeln('Retrieved contents:');
        $output->writeln($data['contents']);
    }

    private function notFound(int $pmid, $output)
    {
        $output->writeln(
            sprintf('<error>No publication with pmid %s</error>', $pmid)
        );
    }
}
