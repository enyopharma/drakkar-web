<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Description;
use Domain\Payloads\InputNotValid;
use Domain\Payloads\ResourceDeleted;
use Domain\Payloads\ResourceNotFound;
use Domain\Payloads\DomainPayloadInterface;

final class DeleteDescription implements DomainActionInterface
{
    const DELETE_DESCRIPTION_SQL = <<<SQL
        UPDATE descriptions SET deleted_at = NOW() WHERE id = ?
SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(array $input): DomainPayloadInterface
    {
        $run_id = (int) $input['run_id'];
        $pmid = (int) $input['pmid'];
        $id = (int) $input['id'];

        $delete_description_sth = $this->pdo->prepare(self::DELETE_DESCRIPTION_SQL);

        $delete_description_sth->execute([$id]);

        return $delete_description_sth->rowCount() == 1
            ? new ResourceDeleted(new Description($run_id, $pmid, $id))
            : new ResourceNotFound('description', [
                'run_id' => $run_id,
                'pmid' => $pmid,
                'id' => $id,
            ]);
    }
}
