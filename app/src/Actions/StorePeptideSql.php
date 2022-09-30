<?php

declare(strict_types=1);

namespace App\Actions;

use App\Input\Peptide;

final class StorePeptideSql implements StorePeptideInterface
{
    const SELECT_DESCRIPTION_SQL = <<<SQL
        SELECT d.id, p2.type AS type2, d.stable_id, d.mapping1, d.mapping2
        FROM runs AS r, associations AS a, descriptions AS d, proteins AS p2
        WHERE r.id = a.run_id
        AND a.id = d.association_id
        AND p2.id = d.protein2_id
        AND r.id = ?
        AND a.pmid = ?
        AND d.id = ?
    SQL;

    const SELECT_PEPTIDE_SQL = <<<SQL
        SELECT * FROM peptides WHERE stable_id = ? AND type = ? AND sequence = ?
    SQL;

    const INSERT_PEPTIDE_SQL = <<<SQL
        INSERT INTO peptides (description_id, stable_id, type, sequence, data) VALUES (?, ?, ?, ?, ?)
    SQL;

    const UPDATE_PEPTIDE_SQL = <<<SQL
        UPDATE peptides SET description_id = ?, data = ? WHERE stable_id = ? AND type = ? AND sequence = ?
    SQL;

    public function __construct(
        private \PDO $pdo,
    ) {
    }

    public function store(int $run_id, int $pmid, int $description_id, Peptide $peptide): StorePeptideResult
    {
        // exctract data from the peptide.
        $type = $peptide->type->value();
        $sequence = $peptide->sequence->value();
        $data = $peptide->data();

        // select description.
        if (!$description = $this->description($run_id, $pmid, $description_id)) {
            return StorePeptideResult::descriptionNotFound($run_id, $pmid, $description_id);
        }

        // check if the peptide actually belongs to the description.
        if (!in_array($sequence, $description['peptides'][$type])) {
            return StorePeptideResult::peptideNotFound($description['stable_id'], $type, $sequence);
        }

        // prepare queries.
        $select_peptide_sth = $this->pdo->prepare(self::SELECT_PEPTIDE_SQL);
        $insert_peptide_sth = $this->pdo->prepare(self::INSERT_PEPTIDE_SQL);
        $update_peptide_sth = $this->pdo->prepare(self::UPDATE_PEPTIDE_SQL);

        // insert peptide data.
        $json = json_encode($data);

        $select_peptide_sth->execute([$description['stable_id'], $type, $sequence]);

        if (!$select_peptide_sth->fetch()) {
            $insert_peptide_sth->execute([$description['id'], $description['stable_id'], $type, $sequence, $json]);
        } else {
            $update_peptide_sth->execute([$description['id'], $json, $description['stable_id'], $type, $sequence]);
        }

        return StorePeptideResult::success();
    }

    private function description(int $run_id, int $pmid, int $description_id): array|false
    {
        $select_description_sth = $this->pdo->prepare(self::SELECT_DESCRIPTION_SQL);

        $select_description_sth->execute([$run_id, $pmid, $description_id]);

        if (!$description = $select_description_sth->fetch()) {
            return false;
        }

        $peptides = ['h' => [], 'v' => []];

        $mapping1 = json_decode($description['mapping1'], true);
        $mapping2 = json_decode($description['mapping2'], true);

        foreach ($mapping1 as $alignment) {
            $peptides['h'][$alignment['sequence']] = 1;
        }

        if ($description['type2'] === 'h') {
            foreach ($mapping2 as $alignment) {
                $peptides['h'][$alignment['sequence']] = 1;
            }
        }

        if ($description['type2'] === 'v') {
            foreach ($mapping2 as $alignment) {
                $peptides['v'][$alignment['sequence']] = 1;
            }
        }

        return [
            'id' => $description['id'],
            'stable_id' => $description['stable_id'],
            'peptides' => [
                'h' => array_keys($peptides['h']),
                'v' => array_keys($peptides['v']),
            ],
        ];
    }
}
