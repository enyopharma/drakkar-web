<?php declare(strict_types=1);

namespace App\Domain;

use App\Queue\Jobs\PopulatePublicationHandler;

use Enyo\Queue\Job;
use Enyo\Queue\Client;

final class PopulateRun
{
    const NOT_FOUND = 0;
    const ALREADY_POPULATED = 1;

    const SELECT_RUN_SQL = <<<SQL
        SELECT * FROM runs WHERE id = ?
SQL;

    const SELECT_PUBLICATIONS_SQL = <<<SQL
        SELECT p.*
        FROM publications AS p, associations AS a
        WHERE p.id = a.publication_id AND a.run_id = ?
        AND p.populated IS FALSE
SQL;

    private $pdo;

    private $client;

    public function __construct(\PDO $pdo, Client $client)
    {
        $this->pdo = $pdo;
        $this->client = $client;
    }

    public function __invoke(int $id): DomainPayloadInterface
    {
        $select_run_sth = $this->pdo->prepare(self::SELECT_RUN_SQL);
        $select_publications_sth = $this->pdo->prepare(self::SELECT_PUBLICATIONS_SQL);

        $select_run_sth->execute([$id]);

        if (! $run = $select_run_sth->fetch()) {
            return new DomainPayload(self::NOT_FOUND);
        }

        $select_publications_sth->execute([$run['id']]);

        while ($publication = $select_publications_sth->fetch()) {
            $this->client->enqueue(new Job(PopulatePublicationHandler::class, [
                'pmid' => $publication['pmid'],
            ]));
        }

        return $select_publications_sth->rowCount() == 0
            ? new DomainPayload(self::ALREADY_POPULATED)
            : new DomainSuccess;
    }
}
