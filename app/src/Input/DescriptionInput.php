<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\OfType;
use Quanta\Validation\ErrorList;
use Quanta\Validation\ArrayFactory;
use Quanta\Validation\InvalidDataException;

use App\Assertions\RunType;
use App\Assertions\ProteinType;

final class DescriptionInput
{
    const SELECT_ASSOCIATION_SQL = <<<SQL
        SELECT r.type FROM runs AS r, associations AS a WHERE r.id = a.run_id AND a.id = ?
    SQL;

    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT id, association_id FROM descriptions WHERE stable_id = ?
    SQL;

    const SELECT_METHOD_SQL = <<<SQL
        SELECT id FROM methods WHERE id = ?
    SQL;

    const STABLE_ID_PATTERN = '/^EY[A-Z0-9]{8}$/';

    private int $association_id;
    
    private string $stable_id;

    private int $method_id;

    private InteractorInput $interactor1;

    private InteractorInput $interactor2;

    public static function factory(int $association_id): callable
    {
        $interactor = InteractorInput::factory();

        $is_str = OfType::guard('string');
        $is_int = OfType::guard('int');
        $is_arr = OfType::guard('array');

        return new ArrayFactory(fn (...$xs) => self::from($association_id, ...$xs),
            Field::required('stable_id', $is_str)->focus(),
            Field::required('method_id', $is_int)->focus(),
            Field::required('interactor1', $is_arr, $interactor)->focus(),
            Field::required('interactor2', $is_arr, $interactor)->focus(),
        );
    }

    public static function from(int $association_id, string $stable_id, int $method_id, InteractorInput $interactor1, InteractorInput $interactor2): self
    {
        $input = new self($association_id, $stable_id, $method_id, $interactor1, $interactor2);

        $errors = [
            ...$input->validateStableId()->errors('stable_id'),
            ...$input->validateMethodId()->errors('method_id'),
        ];;

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(int $association_id, string $stable_id, int $method_id, InteractorInput $interactor1, InteractorInput $interactor2)
    {
        $this->association_id = $association_id;
        $this->stable_id = $stable_id;
        $this->method_id = $method_id;
        $this->interactor1 = $interactor1;
        $this->interactor2 = $interactor2;
    }

    public function data(): array
    {
        return [
            'association_id' => $this->association_id,
            'stable_id' => $this->stable_id,
            'method_id' => $this->method_id,
            'interactor1' => $this->interactor1->data(),
            'interactor2' => $this->interactor2->data(),
        ];
    }

    private function validateStableId(): ErrorList
    {
        $errors = strlen($this->stable_id) > 0 && preg_match(self::STABLE_ID_PATTERN, $this->stable_id) === 0
            ? [new Error(sprintf('must match %s', self::STABLE_ID_PATTERN))]
            : [];

        return new ErrorList(...$errors);
    }

    private function validateMethodId(): ErrorList
    {
        $errors = $this->method_id < 1
            ? [new Error('must be positive')]
            : [];
        
        return new ErrorList(...$errors);
    }

    public function validateForDb(\PDO $pdo): ErrorList
    {
        $select_association_sth = $pdo->prepare(self::SELECT_ASSOCIATION_SQL);

        $select_association_sth->execute([$this->association_id]);

        $association = $select_association_sth->fetch();

        if (!$association) {
            throw new \LogicException('invalid association id');
        }

        $type1 = ProteinType::H;
        $type2 = $association['type'] == RunType::HH ? ProteinType::H : ProteinType::V;

        return new ErrorList(
            ...$this->validateStableIdForDb($pdo)->errors('stable_id'),
            ...$this->validateMethodIdForDb($pdo)->errors('method_id'),
            ...$this->interactor1->validateForDbAndType($pdo, $type1)->errors('interactor1'),
            ...$this->interactor2->validateForDbAndType($pdo, $type2)->errors('interactor2'),
        );
    }

    private function validateStableIdForDb(\PDO $pdo): ErrorList
    {
        if ($this->stable_id == '') {
            return new ErrorList;
        }

        $select_descriptions_sth = $pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute([$this->stable_id]);

        $description = $select_descriptions_sth->fetch();

        if (!$description) {
            return new ErrorList(new Error('must exist'));
        }

        if ($description['association_id'] != $this->association_id) {
            return new ErrorList(new Error('must be associated to the same publication'));
        }

        return new ErrorList;
    }

    private function validateMethodIdForDb(\PDO $pdo): ErrorList
    {
        $select_method_sth = $pdo->prepare(self::SELECT_METHOD_SQL);

        $select_method_sth->execute([$this->method_id]);

        $method = $select_method_sth->fetch();

        if (!$method) {
            return new ErrorList(new Error('must exist'));
        }

        return new ErrorList;
    }
}
