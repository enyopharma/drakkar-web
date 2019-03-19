<?php declare(strict_types=1);

namespace App\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Domain\InsertRun;

use Enyo\Cli\Responder;

abstract class AbstractCreateRunCommand extends Command
{
    private $type;

    private $insert;

    private $responder;

    public function __construct(string $type, InsertRun $insert, Responder $responder)
    {
        $this->type = $type;
        $this->insert = $insert;
        $this->responder = $responder;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(sprintf('Create a %s curation run', strtoupper($this->type)))
            ->setHelp('PMID associated to the curation run are read from STDIN')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the curation run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        try {
            $pmids = $this->pmidsFromStdin();
        }

        catch (\UnexpectedValueException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 0;
        }

        $payload = ($this->insert)($this->type, $name, ...$pmids);

        return $payload->parsed($this->bind('success', $output), [
            InsertRun::INVALID_TYPE => $this->bind('invalidType', $output),
            InsertRun::NOT_UNIQUE => $this->bind('notUnique', $output),
        ]);
    }

    private function bind(string $method, ...$xs)
    {
        return function ($data) use ($method, $xs) {
            return $this->{$method}(...array_merge($xs, [$data]));
        };
    }

    private function success(OutputInterface $output, array $data): void
    {
        $this->responder->info('Curation run inserted with id %s.', ...[
            $output,
            $data['run']['id'],
        ]);
    }

    private function invalidType(OutputInterface $output): void
    {
        $this->responder->error('Value \'%s\' is not a valid curation run type.', ...[
            $output,
            $this->type,
        ]);
    }

    private function notUnique(OutputInterface $output, array $data): void
    {
        $this->responder->error('Publication with PMID %s is already associated to a %s curation run (\'%s\').', ...[
            $output,
            $data['publication']['pmid'],
            $this->type,
            $data['run']['name'],
        ]);
    }

    private function pmidsFromStdin(): array
    {
        $pmids = [];

        $stdin = fopen("php://stdin", "r");

        try {
            while ($stdin && $line = fgets($stdin)) {
                $line = rtrim($line);

                if (empty($line)) continue;

                if (! preg_match('/^[0-9]+$/', $line)) {
                    throw new \UnexpectedValueException(
                        vsprintf('Value \'%s\' from stdin is not a valid PMID', [
                            strlen($line) > 10 ? substr($line, 0, 10) . '...' : $line,
                        ])
                    );
                }

                $pmids[(int) $line] = true;
            }
        }

        catch (\UnexpectedValueException $e) {
            throw $e;
        }

        finally {
            $stdin && fclose($stdin);
        }

        return array_keys($pmids);
    }
}
