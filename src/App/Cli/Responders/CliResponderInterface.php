<?php

declare(strict_types=1);

namespace App\Cli\Responders;

use Symfony\Component\Console\Output\OutputInterface;

use Domain\Payloads\DomainPayloadInterface;

interface CliResponderInterface
{
    public function __invoke(OutputInterface $output, DomainPayloadInterface $payload);
}
