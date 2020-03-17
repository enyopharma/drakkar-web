<?php

declare(strict_types=1);

namespace App\Cli\Responders;

use Symfony\Component\Console\Output\OutputInterface;

use Domain\Actions\PopulatePublicationResult;

final class PopulatePublicationResponder
{
    /**
     * @return mixed
     */
    public function write(OutputInterface $output, int $pmid, PopulatePublicationResult $result)
    {
        return $result->match([
            PopulatePublicationResult::SUCCESS => function () use ($output, $pmid) {
                return $output->writeln(
                    sprintf('<info>Metadata of publication with [\'pmid\' => %s] successfully updated.</info>', $pmid)
                );
            },
            PopulatePublicationResult::ALREADY_POPULATED => function () use ($output, $pmid) {
                return $output->writeln(
                    sprintf('<info>Metadata of publication with [\'pmid\' => %s] already populated</info>', $pmid)
                );
            },
            PopulatePublicationResult::NOT_FOUND => function () use ($output, $pmid) {
                return $output->writeln(
                    sprintf('<error>No publication with [\'pmid\' => %s]</error>', $pmid)
                );
            },
            PopulatePublicationResult::PARSING_ERROR => function (string $message) use ($output, $pmid) {
                return $output->writeln(
                    sprintf('<error>Failed to retrieve metadata of publication with [\'pmid\' => %s] - %s</error>', $pmid, $message)
                );
            },
        ]);
    }
}
