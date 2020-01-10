<?php

declare(strict_types=1);

namespace App\Cli\Responders;

use Symfony\Component\Console\Output\OutputInterface;

use Domain\Services\PublicationMetadataResult;

final class PublicationMetadataResponder
{
    /**
     * @return mixed
     */
    public function write(OutputInterface $output, PublicationMetadataResult $result)
    {
        return $result->match([
            PublicationMetadataResult::SUCCESS => function (int $pmid) use ($output) {
                return $output->writeln(
                    sprintf('<info>Metadata of publication with [\'pmid\' => %s] successfully updated.</info>', $pmid)
                );
            },
            PublicationMetadataResult::ALREADY_POPULATED => function (int $pmid) use ($output) {
                return $output->writeln(
                    sprintf('<info>Metadata of publication with [\'pmid\' => %s] already populated</info>', $pmid)
                );
            },
            PublicationMetadataResult::NOT_FOUND => function (int $pmid) use ($output) {
                return $output->writeln(
                    sprintf('<error>No publication with [\'pmid\' => %s]</error>', $pmid)
                );
            },
            PublicationMetadataResult::PARSING_ERROR => function (int $pmid, string $message) use ($output) {
                return $output->writeln(
                    sprintf('<error>Failed to retrieve metadata of publication with [\'pmid\' => %s] - %s</error>', $pmid, $message)
                );
            },
        ]);
    }
}
