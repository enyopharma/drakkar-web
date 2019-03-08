<?php declare(strict_types=1);

$queries['runs/insert'] = <<<SQL
    INSERT INTO runs (type, name) VALUES (?, ?)
SQL;

$queries['runs/find'] = <<<SQL
    SELECT * FROM runs
    WHERE state = ? AND id = ? AND deleted_at IS NULL
SQL;

$queries['runs/select'] = <<<SQL
    SELECT * FROM runs
    WHERE state = ? AND deleted_at IS NULL
    GROUP BY id
    ORDER BY created_at DESC, id DESC
SQL;

$queries['runs.publications/insert'] = <<<SQL
    INSERT INTO associations (run_id, publication_id) VALUES (?, ?)
SQL;

$queries['runs.publications/update'] = <<<SQL
    UPDATE associations
    SET state = ?, annotation = ?, updated_at = NOW()
    WHERE run_id = ?
    AND publication_id = ?
SQL;

$queries['runs.publications/count'] = <<<SQL
    SELECT a.run_id, COUNT(p.id)
    FROM associations AS a, publications AS p
    WHERE p.id = a.publication_id AND a.run_id = ? AND a.state = ?
    GROUP BY a.run_id
SQL;

$queries['runs.publications/count.eagerload'] = <<<SQL
    SELECT a.run_id, COUNT(p.id)
    FROM associations AS a, publications AS p
    WHERE p.id = a.publication_id AND a.state = ?
    GROUP BY a.run_id
SQL;

$queries['runs.publications/select'] = <<<SQL
    SELECT a.run_id, p.*, a.state, a.annotation
    FROM associations AS a, publications AS p
    WHERE p.id = a.publication_id
    AND a.run_id = ?
    AND a.state = ?
    ORDER BY a.updated_at DESC, a.id ASC
    LIMIT ? OFFSET ?
SQL;

$queries['publications/insert'] = <<<SQL
    INSERT INTO publications (pmid) VALUES (?)
SQL;

return $queries;
