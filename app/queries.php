<?php declare(strict_types=1);

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

$queries['runs/count.publications'] = <<<SQL
    SELECT a.run_id, COUNT(p.id)
    FROM associations AS a, publications AS p
    WHERE p.id = a.publication_id AND a.state = ?
    GROUP BY a.run_id
SQL;

$queries['publications/update'] = <<<SQL
    UPDATE associations
    SET state = ?, annotation = ?, updated_at = NOW()
    WHERE run_id = ?
    AND publication_id = ?
SQL;

$queries['publications/count.from_run'] = <<<SQL
    SELECT a.run_id, COUNT(p.id)
    FROM associations AS a, publications AS p
    WHERE p.id = a.publication_id AND a.run_id = ? AND a.state = ?
    GROUP BY a.run_id
SQL;

$queries['publications/select.from_run'] = <<<SQL
    SELECT a.run_id, p.*, a.state, a.annotation
    FROM associations AS a, publications AS p
    WHERE p.id = a.publication_id
    AND a.run_id = ?
    AND a.state = ?
    ORDER BY a.updated_at DESC, a.id ASC
    LIMIT ? OFFSET ?
SQL;

return $queries;
