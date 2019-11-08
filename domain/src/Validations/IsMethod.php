<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

final class IsMethod
{
    const PSIMI_ID_PATTERN = '/^MI:[0-9]{4}$/';

    const SELECT_METHOD_SQL = <<<SQL
        SELECT * FROM methods WHERE psimi_id = ?
    SQL;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(array $data): InputInterface
    {
        $slice = new Slice;
        $isstr = new IsTypedAs('string');
        $isnotempty = new IsNotEmpty;
        $ismatching = new IsMatching(self::PSIMI_ID_PATTERN);
        $ismethod = \Closure::fromCallable([$this, 'isMethod']);

        $factory = Input::pure(fn (string $psimi_id) => compact('psimi_id'));

        $psimi_id = $slice($data, 'psimi_id')->validate($isstr, $isnotempty, $ismatching, $ismethod);

        return $factory($psimi_id);
    }

    public function isMethod(string $psimi_id): InputInterface
    {
        $select_method_sth = $this->pdo->prepare(self::SELECT_METHOD_SQL);

        $select_method_sth->execute([$psimi_id]);

        return $select_method_sth->fetch()
            ? new Success($psimi_id)
            : new Failure(new Error('%%s => no method with psimi id %s', $psimi_id));
    }
}
