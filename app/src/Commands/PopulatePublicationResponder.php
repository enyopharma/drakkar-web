<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Output\OutputInterface;

use App\Actions\PopulatePublicationResult;

final class PopulatePublicationResponder
{
    public function write(OutputInterface $output, int $pmid, PopulatePublicationResult $result): int
    {
        return $result->match([
            PopulatePublicationResult::SUCCESS => function () use ($output, $pmid) {
                $output->writeln(
                    sprintf('<info>Metadata of publication with [\'pmid\' => %s] successfully updated.</info>', $pmid)
                );

                return 0;
            },
            PopulatePublicationResult::ALREADY_POPULATED => function () use ($output, $pmid) {
                $output->writeln(
                    sprintf('<info>Metadata of publication with [\'pmid\' => %s] already populated</info>', $pmid)
                );

                return 1;
            },
            PopulatePublicationResult::NOT_FOUND => function () use ($output, $pmid) {
                $output->writeln(
                    sprintf('<error>No publication with [\'pmid\' => %s]</error>', $pmid)
                );

                return 1;
            },
            PopulatePublicationResult::PARSING_ERROR => function (string $message) use ($output, $pmid) {
                $output->writeln(
                    sprintf('<error>Failed to retrieve metadata of publication with [\'pmid\' => %s] - %s</error>', $pmid, $message)
                );

                return 1;
            },
        ]);
    }
}
