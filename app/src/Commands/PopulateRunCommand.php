<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Actions\PopulatePublicationInterface;

final class PopulateRunCommand extends Command
{
    const SELECT_RUN_SQL = <<<SQL
        SELECT * FROM runs WHERE id = ?
    SQL;

    const SELECT_PUBLICATIONS_SQL = <<<SQL
        SELECT p.*
        FROM publications AS p, associations AS a
        WHERE p.pmid = a.pmid AND a.run_id = ?
        AND p.populated IS FALSE
    SQL;

    const UPDATE_RUN_SQL = <<<SQL
        UPDATE runs SET populated = TRUE WHERE id = ?
    SQL;

    protected static $defaultName = 'runs:populate';

    private \PDO $pdo;

    private PopulatePublicationInterface $action;

    private PopulatePublicationResponder $responder;

    public function __construct(\PDO $pdo, PopulatePublicationInterface $action, PopulatePublicationResponder $responder)
    {
        $this->pdo = $pdo;
        $this->action = $action;
        $this->responder = $responder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Populate the metadata of the publications of a curation run')
            ->setHelp('Metadata are downloaded from pubmed')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the curation run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = (int) ((array) $input->getArgument('id'))[0];

        // prepare the queries.
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);
        $select_publications_sth = $this->pdo->prepare(self::SELECT_PUBLICATIONS_SQL);
        $update_run_sth = $this->pdo->prepare(self::UPDATE_RUN_SQL);

        // select the curation run.
        $select_run_sth->execute([$id]);

        if (!$run = $select_run_sth->fetch()) {
            return $this->runNotFoundOutput($output, $id);
        }

        if ($run['populated']) {
            return $this->runalreadyPopulatedOutput($output, $run);
        }

        // populate the run publications.
        $errors = 0;

        $select_publications_sth->execute([$run['id']]);

        while ($publication = $select_publications_sth->fetch()) {
            $result = $this->action->populate($publication['pmid']);

            $this->responder->write($output, $publication['pmid'], $result);

            if (!$result->isSuccess()) {
                $errors++;
            }
        }

        // write a failure when there is errors.
        if ($errors > 0) {
            return $this->failureOutput($output, $run);
        }

        // success !
        return $this->successOutput($output, $run);
    }

    private function runNotfoundOutput(OutputInterface $output, int $id): int
    {
        $output->writeln(
            sprintf('<error>No run with [\'id\' => %s]</error>', $id)
        );

        return 1;
    }

    private function runalreadyPopulatedOutput(OutputInterface $output, array $run): int
    {
        $output->writeln(
            sprintf('<info>Metadata of curation run \'%s\' publications are already populated</info>', $run['name'])
        );

        return 1;
    }

    private function failureOutput(OutputInterface $output, array $run): int
    {
        $output->writeln(
            sprintf('<error>Failed to retrieve metadata of run \'%s\' publications</error>', $run['name'])
        );

        return 1;
    }

    private function successOutput(OutputInterface $output, array $run): int
    {
        $output->writeln(
            sprintf('<info>Metadata of curation run \'%s\' publications successfully updated.</info>', $run['name'])
        );

        return 0;
    }
}
