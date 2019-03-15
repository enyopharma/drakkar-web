<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Domain\UpdatePublicationMetadata;

final class PopulatePublicationCommand extends Command
{
    protected static $defaultName = 'publications:populate';

    private $domain;

    public function __construct(UpdatePublicationMetadata $domain)
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
            UpdatePublicationMetadata::QUERY_FAILED => $this->bind('queryFailed', $pmid, $output),
            UpdatePublicationMetadata::PARSING_FAILED => $this->bind('parsingFailed', $output),
            UpdatePublicationMetadata::NOT_FOUND => $this->bind('notFound', $pmid, $output),
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

    private function parsingFailed($output, array $data)
    {
        $output->writeln(sprintf('<error>Metadata parsing failed with code %s</error>', $data['error']));
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