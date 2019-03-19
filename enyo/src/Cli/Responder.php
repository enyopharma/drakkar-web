<?php declare(strict_types=1);

namespace Enyo\Cli;

use Symfony\Component\Console\Output\OutputInterface;

final class Responder
{
    public function default(string $tpl, OutputInterface $output, ...$xs): void
    {
        $output->writeln(sprintf($tpl, ...$xs));
    }

    public function info(string $tpl, OutputInterface $output, ...$xs): void
    {
        $output->writeln(sprintf('<info>%s</info>', sprintf($tpl, ...$xs)));
    }

    public function error(string $tpl, OutputInterface $output, ...$xs): void
    {
        $output->writeln(sprintf('<error>%s</error>', sprintf($tpl, ...$xs)));
    }
}
