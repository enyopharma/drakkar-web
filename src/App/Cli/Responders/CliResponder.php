<?php

declare(strict_types=1);

namespace App\Cli\Responders;

use Symfony\Component\Console\Output\OutputInterface;

use Domain\Payloads\DomainPayloadInterface;

final class CliResponder implements CliResponderInterface
{
    public function __invoke(OutputInterface $output, DomainPayloadInterface $payload)
    {
        if ($payload instanceof \Domain\Payloads\ResourceCreated) {
            return $this->resourceCreated($output, $payload);
        }

        if ($payload instanceof \Domain\Payloads\ResourceUpdated) {
            return $this->resourceUpdated($output, $payload);
        }

        if ($payload instanceof \Domain\Payloads\ResourceNotFound) {
            return $this->resourceNotFound($output, $payload);
        }

        if ($payload instanceof \Domain\Payloads\InputNotValid) {
            return $this->inputNotValid($output, $payload);
        }

        if ($payload instanceof \Domain\Payloads\DomainConflict) {
            return $this->domainFailure($output, $payload);
        }

        if ($payload instanceof \Domain\Payloads\RuntimeFailure) {
            return $this->domainFailure($output, $payload);
        }

        throw new \LogicException(
            sprintf('Unhandled payload %s', get_class($payload))
        );
    }

    private function resourceCreated($output, $payload)
    {
        $tpl = '<info>Curation run created with %s.</info>';

        return $output->writeln(sprintf($tpl, $payload->idstr()));
    }

    private function resourceUpdated($output, $payload)
    {
        if ($payload->isAbout(\Domain\Run::class)) {
            $tpl = '<info>Metadata of curation run with %s publications successfully updated.</info>';

            $output->writeln(sprintf($tpl, $payload->idstr()));
        }

        if ($payload->isAbout(\Domain\Publication::class)) {
            $tpl = '<info>Metadata of publication with %s successfully updated.</info>';

            $output->writeln(sprintf($tpl, $payload->idstr()));
        }
    }

    private function resourceNotFound($output, $payload)
    {
        $output->writeln(sprintf('<error>%s</error>', $payload->message()));
    }

    private function inputNotValid($output, $payload)
    {
        foreach ($payload->errors() as $error) {
            $output->writeln(sprintf('<error>%s</error>', $error));
        }

        return 1;
    }

    private function domainFailure($output, $payload)
    {
        $output->writeln(sprintf('<error>%s</error>', $payload->reason()));
    }
}
