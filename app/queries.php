<?php declare(strict_types=1);

$queries['runs/insert'] = <<<SQL
    INSERT INTO runs (type, name) VALUES (?, ?)
SQL;

$queries['runs/find'] = <<<SQL
    SELECT * FROM runs
    WHERE id = ? AND populated IS TRUE AND deleted_at IS NULL
SQL;

$queries['runs/select'] = <<<SQL
    SELECT * FROM runs
    WHERE populated IS TRUE AND deleted_at IS NULL
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

$queries['publications/find.pmid'] = <<<SQL
    SELECT * FROM publications WHERE pmid = ?
SQL;

$queries['publications/select.type.pmids'] = <<<SQL
    SELECT r.id as run_id, r.name as run_name, p.*
    FROM runs AS r, publications AS p, associations AS a
    WHERE r.id = a.run_id AND r.type = ?
    AND p.id = a.publication_id AND p.pmid IN(%s)
    GROUP BY r.id, p.id
SQL;

return $queries;
