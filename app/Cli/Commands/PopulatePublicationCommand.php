<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Domain\PopulatePublication;
use App\Domain\Services\Efetch;

use App\Cli\Responders\PopulateResponder;

final class PopulatePublicationCommand extends Command
{
    protected static $defaultName = 'publications:populate';

    private $domain;

    private $responder;

    public function __construct(PopulatePublication $domain, PopulateResponder $responder)
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

        return ($this->domain)($pmid)->parsed($this->bind('success', $output, $pmid), [
            PopulatePublication::NOT_FOUND => $this->bind('notFound', $output, $pmid),
            PopulatePublication::ALREADY_POPULATED => $this->bind('alreadyPopulated', $output, $pmid),
            PopulatePublication::EFETCH_ERROR => $this->bind('efetchError', $output, $pmid),
        ]);
    }

    private function bind(string $method, ...$xs)
    {
        return function ($data) use ($method, $xs) {
            return $this->{$method}(...array_merge($xs, [$data]));
        };
    }

    private function success(OutputInterface $output, int $pmid)
    {
        $this->responder->success($output, $pmid);
    }

    private function notFound(OutputInterface $output, int $pmid)
    {
        $this->responder->error('No publication with pmid %s.', $output, $pmid);
    }

    private function alreadyPopulated(OutputInterface $output, int $pmid)
    {
        $this->responder->info('Metadata of publication with pmid %s are already updated.', ...[
            $output,
            $pmid,
        ]);
    }

    private function efetchError(OutputInterface $output, int $pmid, array $data)
    {
        $this->responder->efetchError($output, $pmid, $data);
    }
}
