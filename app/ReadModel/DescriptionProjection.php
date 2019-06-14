<?php declare(strict_types=1);

namespace App\ReadModel;

use Enyo\ReadModel\NotFoundException;

final class DescriptionProjection
{
    const SELECT_FROM_ID_SQL = <<<SQL
        SELECT d.*
        FROM associations AS a, descriptions AS d
        WHERE a.id = d.association_id
        AND a.id = ?
        AND a.pmid = ?
        AND d.id = ?
SQL;

    private $pdo;

    private $methods;

    private $interactors;

    public function __construct(\PDO $pdo, MethodProjection $methods, InteractorProjection $interactors)
    {
        $this->pdo = $pdo;
        $this->methods = $methods;
        $this->interactors = $interactors;
    }

    public function id(int $run_id, int $pmid, int $id): array
    {
        $select_description_sth = $this->pdo->prepare(self::SELECT_FROM_ID_SQL);

        $select_description_sth->execute([$run_id, $pmid, $id]);

        if ($description = $select_description_sth->fetch()) {
            return [
                'run_id' => $run_id,
                'pmid' => $pmid,
                'method' => $this->methods->id($description['method_id']),
                'interactor1' => $this->interactors->id($description['interactor1_id']),
                'interactor2' => $this->interactors->id($description['interactor2_id']),
            ];
        }

        throw new NotFoundException(
            vsprintf('%s has no entry with run_id %s, pmid %s and id %s', [
                self::class,
                $run_id,
                $pmid,
                $id,
            ])
        );
    }
}
