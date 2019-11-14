<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\HasType;
use Quanta\Validation\Rules\ArrayKeys;
use Quanta\Validation\Rules\IsNotEmpty;
use Quanta\Validation\Rules\IsMatching;

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
        $isstr = new HasType('string');
        $isnotempty = new IsNotEmpty;
        $ispsimiid = new IsMatching(self::PSIMI_ID_PATTERN);
        $isexisting = \Closure::fromCallable([$this, 'isExistingPsimiId']);

        $makeIsoform = new ArrayKeys([
            'psimi_id' => [$isstr, $isnotempty, $ispsimiid, $isexisting],
        ]);

        return $makeIsoform($data);
    }

    public function isExistingPsimiId(string $psimi_id): InputInterface
    {
        $select_method_sth = $this->pdo->prepare(self::SELECT_METHOD_SQL);

        $select_method_sth->execute([$psimi_id]);

        return $select_method_sth->fetch()
            ? Input::unit($psimi_id)
            : new Failure(new Error(sprintf('no method with psimi id %s', $psimi_id)));
    }
}
