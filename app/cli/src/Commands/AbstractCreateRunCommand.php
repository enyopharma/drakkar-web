<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use Domain\Actions\CreateRun;

use App\Cli\Responders\RunResponder;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCreateRunCommand extends Command
{
    private $type;

    private $domain;

    private $responder;

    public function __construct(string $type, CreateRun $domain, RunResponder $responder)
    {
        $this->type = $type;
        $this->domain = $domain;
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
        $input = [
            'type' => $this->type,
            'name' => $input->getArgument('name'),
        ];

        try {
            $input['pmids'] = $this->pmidsFromStdin();
        }

        catch (\UnexpectedValueException $e) {
            return $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }

        $payload = ($this->domain)($input);

        return ($this->responder)($output, $payload);
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
