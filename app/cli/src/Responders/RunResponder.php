<?php

declare(strict_types=1);

namespace App\Cli\Responders;

use Symfony\Component\Console\Output\OutputInterface;

use Domain\Payloads\InputNotValid;
use Domain\Payloads\DomainConflict;
use Domain\Payloads\RuntimeFailure;
use Domain\Payloads\ResourceCreated;
use Domain\Payloads\ResourceUpdated;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;

final class RunResponder implements CliResponderInterface
{
    public function __invoke(OutputInterface $output, DomainPayloadInterface $payload): int
    {
        if ($payload instanceof ResourceCreated) {
            return $this->resourceCreated($output, $payload);
        }

        if ($payload instanceof ResourceUpdated) {
            return $this->resourceUpdated($output, $payload);
        }

        if ($payload instanceof ResourceNotFound) {
            return $this->resourceNotFound($output, $payload);
        }

        if ($payload instanceof InputNotValid) {
            return $this->inputNotValid($output, $payload);
        }

        if ($payload instanceof DomainConflict) {
            return $this->domainConflict($output, $payload);
        }

        if ($payload instanceof RuntimeFailure) {
            return $this->runtimeFailure($output, $payload);
        }

        throw new \LogicException(
            sprintf('Unhandled payload %s', get_class($payload))
        );
    }

    private function resourceCreated(OutputInterface $output, ResourceCreated $payload): int
    {
        $tpl = '<info>Curation run created with %s.</info>';

        return $output->writeln(sprintf($tpl, $payload->idstr()));
    }

    private function resourceUpdated(OutputInterface $output, ResourceUpdated $payload): int
    {
        $tpl = '<info>Metadata of curation run with %s publications successfully updated.</info>';

        return $output->writeln(sprintf($tpl, $payload->idstr()));
    }

    private function resourceNotFound(OutputInterface $output, ResourceNotFound $payload): int
    {
        return $output->writeln(sprintf('<error>%s</error>', $payload->message()));
    }

    private function inputNotValid(OutputInterface $output, InputNotValid $payload): int
    {
        foreach ($payload->errors() as $error) {
            $output->writeln(sprintf('<error>%s</error>', $error));
        }

        return 1;
    }

    private function domainConflict(OutputInterface $output, DomainConflict $payload): int
    {
        return $output->writeln(sprintf('<error>%s</error>', $payload->reason()));
    }

    private function runtimeFailure(OutputInterface $output, RuntimeFailure $payload): int
    {
        return $output->writeln(sprintf('<error>%s</error>', $payload->reason()));
    }
}
