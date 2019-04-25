<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Domain\PopulateRun;
use App\Domain\Services\Efetch;

use App\Cli\Responders\PopulateResponder;

final class PopulateRunCommand extends Command
{
    protected static $defaultName = 'runs:populate';

    private $domain;

    private $responder;

    public function __construct(PopulateRun $domain, PopulateResponder $responder)
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

        foreach (($this->domain)($id) as $payload) {
            $payload->parsed($this->success($output, $id), [
                PopulateRun::NOT_FOUND => $this->notFound($output, $id),
                PopulateRun::ALREADY_POPULATED => $this->alreadyPopulated($output, $id),
                PopulateRun::UPDATE_SUCCESS => $this->updateSuccess($output),
                PopulateRun::EFETCH_ERROR => $this->efetchError($output),
                PopulateRun::SOME_FAILED => $this->someFailed($output, $id),
            ]);
        }
    }

    private function success(OutputInterface $output, int $id): callable
    {
        return function () use ($output, $id) {
            $this->responder->info('Metadata of the publications of the curation run with id %s successfully updated.', ...[
                $output,
                $id,
            ]);
        };
    }

    private function notFound(OutputInterface $output, int $id): callable
    {
        return function () use ($output, $id) {
            $this->responder->error('No curation run with id %s.', $output, $id);
        };
    }

    private function alreadyPopulated(OutputInterface $output, int $id): callable
    {
        return function () use ($output, $id) {
            $this->responder->info('Metadata of the publications of the curation run with id %s are already updated.', ...[
                $output,
                $id,
            ]);
        };
    }

    private function updateSuccess(OutputInterface $output): callable
    {
        return function (array $data) use ($output) {
            $this->responder->success($output, $data['pmid']);
        };
    }

    private function efetchError(OutputInterface $output): callable
    {
        return function (array $data) use ($output) {
            $this->responder->efetchError($output, $data['pmid'], $data);
        };
    }

    private function someFailed(OutputInterface $output, int $id): callable
    {
        return function (array $data) use ($output, $id) {
            $this->responder->error('Failed to retrieve metadata of %s publications of the curation run with id %s.', ...[
                $output,
                $data['errors'],
                $id,
            ]);
        };
    }
}
