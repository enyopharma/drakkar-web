<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCreateRunCommand extends Command
{
    const INSERT_RUN_SQL = <<<SQL
        INSERT INTO runs (type, name) VALUES (?, ?)
    SQL;

    const INSERT_PUBLICATION_SQL = <<<SQL
        INSERT INTO publications (pmid) VALUES (?)
    SQL;

    const INSERT_ASSOCIATION_SQL = <<<SQL
        INSERT INTO associations (run_id, pmid) VALUES (?, ?)
    SQL;

    const SELECT_RUN_SQL = <<<SQL
        SELECT * FROM runs WHERE  name = ?
    SQL;

    const SELECT_PUBLICATION_SQL = <<<SQL
        SELECT * FROM publications WHERE pmid = ?
    SQL;

    const SELECT_PUBLICATIONS_SQL = <<<SQL
        SELECT r.id AS run_id, r.type AS run_type, r.name AS run_name, a.pmid
        FROM runs AS r, associations AS a
        WHERE r.id = a.run_id
        AND r.type = ?
        AND a.pmid IN(%s)
    SQL;

    private \PDO $pdo;

    private string $type;

    public function __construct(\PDO $pdo, string $type)
    {
        if (!in_array($type, ['hh', 'vh'])) {
            throw new \InvalidArgumentException(
                sprintf('\'%s\' is not a valid curation run type.', $type)
            );
        }

        $this->pdo = $pdo;
        $this->type = $type;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(sprintf('Create a %s curation run', strtoupper($this->type)))
            ->setHelp('PMID associated to the curation run are read from STDIN')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the curation run.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $this->type;
        $name = $input->getArgument('name');

        try {
            $pmids = $this->pmidsFromStdin();
        }

        catch (\UnexpectedValueException $e) {
            return $this->invalidPmidOutput($output, $e->getMessage());
        }

        if (count($pmids) == 0) {
            return $this->noPmidOutput($output);
        }

        // prepare the queries.
        $insert_run_sth = $this->pdo->prepare(self::INSERT_RUN_SQL);
        $insert_publication_sth = $this->pdo->prepare(self::INSERT_PUBLICATION_SQL);
        $insert_association_sth = $this->pdo->prepare(self::INSERT_ASSOCIATION_SQL);
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);
        $select_publication_sth = $this->pdo->prepare(self::SELECT_PUBLICATION_SQL);
        $select_publications_sth = $this->pdo->prepare(
            vsprintf(self::SELECT_PUBLICATIONS_SQL, [
                implode(', ', array_pad([], count($pmids), '?')),
            ])
        );

        // return an error when a run with the same name already exist.
        $select_run_sth->execute([$name]);

        if ($run = $select_run_sth->fetch()) {
            return $this->runAlreadyExistsOutput($output, $run);
        }

        // return an error when any publication is already associated with a
        // publication curation run of the same type.
        $select_publications_sth->execute(array_merge([$type], $pmids));

        if ($publication = $select_publications_sth->fetch()) {
            return $this->associationAlreadyExistsOutput($output, $publication);
        }

        // insert the curation run, the missing pmids and associations.
        $this->pdo->beginTransaction();

        $insert_run_sth->execute([$type, $name]);

        $run['id'] = (int) $this->pdo->lastInsertId();

        foreach ($pmids as $pmid) {
            $select_publication_sth->execute([$pmid]);

            if (!$select_publication_sth->fetch()) {
                $insert_publication_sth->execute([$pmid]);
            }

            $insert_association_sth->execute([$run['id'], $pmid]);
        }

        $this->pdo->commit();

        // success !
        return $this->successOutput($output, $run);
    }

    private function pmidsFromStdin(): array
    {
        $pmids = [];

        $stdin = fopen('php://stdin', 'r');

        try {
            while ($stdin && $line = fgets($stdin)) {
                $line = rtrim($line);

                if (empty($line)) continue;

                if (!preg_match('/^[0-9]+$/', $line)) {
                    throw new \UnexpectedValueException(
                        vsprintf('Value \'%s\' from stdin is not a valid PMID', [
                            strlen($line) > 10 ? substr($line, 0, 10) . '...' : $line,
                        ])
                    );
                }

                $pmids[(int) $line] = true;
            }
        }

        catch (\UnexpectedValueException $e) {
            throw $e;
        }

        finally {
            $stdin && fclose($stdin);
        }

        return array_keys($pmids);
    }

    private function invalidPmidOutput(OutputInterface $output, string $message): int
    {
        $output->writeln(sprintf('<error>%s</error>', $message));

        return 1;
    }

    private function noPmidOutput(OutputInterface $output): int
    {
        $output->writeln('<error>At least one pmid is required.</error>');

        return 1;
    }

    private function runAlreadyExistsOutput(OutputInterface $output, array $run): int
    {
        $output->writeln(
            sprintf('<error>Run with name \'%s\' already exists</error>', $run['name']),
        );

        return 1;
    }

    private function associationAlreadyExistsOutput(OutputInterface $output, array $publication): int
    {
        $output->writeln(
            vsprintf('<error>Publication with PMID %s is already associated with %s curation run %s (\'%s\')</error>', [
                $publication['pmid'],
                $publication['run_type'],
                $publication['run_id'],
                $publication['run_name'],
            ])
        );

        return 1;
    }

    private function successOutput(OutputInterface $output, array $run): int
    {
        $output->writeln(
            sprintf('<info>Curation run created with [\'id\' => %s].</info>', $run['id'])
        );

        return 0;
    }
}
