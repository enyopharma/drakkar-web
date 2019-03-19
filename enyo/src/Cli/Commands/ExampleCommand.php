<?php declare(strict_types=1);

namespace Enyo\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Enyo\Cli\Responder;

final class ExampleCommand extends Command
{
    protected static $defaultName = 'app:example';

    private $responder;

    public function __construct(Responder $responder)
    {
        $this->responder = $responder;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('This is an example command')
            ->setHelp('This command allows you to have a look on how commands are registered')
            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to say hello to?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name') ?? 'world';

        $this->responder->default('Hello %s.', $output, $name);
    }
}
