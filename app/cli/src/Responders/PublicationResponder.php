<?php

declare(strict_types=1);

namespace App\Cli\Responders;

use Domain\Payloads\DomainPayloadInterface;

use Symfony\Component\Console\Output\OutputInterface;

final class PublicationResponder implements CliResponderInterface
{
    public function __invoke(OutputInterface $output, DomainPayloadInterface $payload)
    {
        if ($payload instanceof \Domain\Payloads\ResourceUpdated) {
            return $this->resourceUpdated($output, $payload);
        }

        if ($payload instanceof \Domain\Payloads\ResourceNotFound) {
            return $this->resourceNotFound($output, $payload);
        }

        if ($payload instanceof \Domain\Payloads\DomainConflict) {
            return $this->domainConflict($output, $payload);
        }

        if ($payload instanceof \Domain\Payloads\RuntimeFailure) {
            return $this->runtimeFailure($output, $payload);
        }

        throw new \LogicException(
            sprintf('Unhandled payload %s', get_class($payload))
        );
    }

    private function resourceUpdated($output, $payload)
    {
        $tpl = '<info>Metadata of publication with %s successfully updated.</info>';

        $output->writeln(sprintf($tpl, $payload->idstr()));
    }

    private function resourceNotFound($output, $payload)
    {
        $output->writeln(sprintf('<error>%s</error>', $payload->message()));
    }

    private function domainConflict($output, $payload)
    {
        $output->writeln(sprintf('<error>%s</error>', $payload->reason()));
    }

    private function runtimeFailure($output, $payload)
    {
        $output->writeln(sprintf('<error>%s</error>', $payload->reason()));
    }
}
