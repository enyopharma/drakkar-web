<?php declare(strict_types=1);

namespace App\Domain;

final class UpdatePublicationMetadata
{
    const QUERY_FAILED = 0;
    const PARSING_FAILED = 1;
    const NOT_FOUND = 2;

    const SELECT_RUNS_SQL = <<<SQL
        SELECT r.*
        FROM runs AS r, associations AS a
        WHERE r.id = a.run_id
        AND a.publication_id = ?
SQL;

    const SELECT_PUBLICATION_SQL = <<<SQL
        SELECT * FROM publications WHERE pmid = ?
SQL;

    const COUNT_NOT_POPULATED_SQL = <<<SQL
        SELECT COUNT(*)
        FROM publications AS p, associations AS a
        WHERE p.id = a.publication_id
        AND a.run_id = ?
        AND p.populated IS FALSE
SQL;

    const UPDATE_RUN_SQL = <<<SQL
        UPDATE runs SET populated = TRUE WHERE id = ?
SQL;

    const UPDATE_PUBLICATION_SQL = <<<SQL
        UPDATE publications
        SET populated = TRUE, metadata = ?
        WHERE pmid = ?
SQL;



    const REMOTE_URL = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi';

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(int $pmid): DomainPayloadInterface
    {
        $select_publication_sth = $this->pdo->prepare(self::SELECT_PUBLICATION_SQL);
        $select_runs_sth = $this->pdo->prepare(self::SELECT_RUNS_SQL);
        $count_not_populated_sth = $this->pdo->prepare(self::COUNT_NOT_POPULATED_SQL);
        $update_publication_sth = $this->pdo->prepare(self::UPDATE_PUBLICATION_SQL);
        $update_run_sth = $this->pdo->prepare(self::UPDATE_RUN_SQL);

        $select_publication_sth->execute([$pmid]);

        if (! $publication = $select_publication_sth->fetch()) {
            return new DomainError(self::NOT_FOUND);
        }

        $contents = file_get_contents(
            sprintf('%s?%s', self::REMOTE_URL, http_build_query([
                'db' => 'pubmed',
                'retmode' => 'json',
                'id' => $pmid,
            ])
        ));

        if ($contents === false) {
            return new DomainError(self::QUERY_FAILED);
        }

        $metadata = json_decode($contents, true);

        if (is_null($metadata)) {
            return new DomainError(self::PARSING_FAILED, [
                'error' => json_last_error(),
                'contents' => $contents,
            ]);
        }

        $this->pdo->beginTransaction();

        $update_publication_sth->execute([
            json_encode($metadata['result'][$pmid] ?? []),
            $pmid,
        ]);

        $select_runs_sth->execute([$publication['id']]);

        while ($run = $select_runs_sth->fetch()) {
            $count_not_populated_sth->execute([$run['id']]);

            if (! $count_not_populated_sth->fetchColumn()) {
                $update_run_sth->execute([$run['id']]);
            }
        }

        $this->pdo->commit();

        return new DomainSuccess;
    }
}
