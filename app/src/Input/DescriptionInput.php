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
            ...$input->validateStableId(),
            ...$input->validateMethodId(),
        ];;

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(
        private int $association_id,
        private string $stable_id,
        private int $method_id,
        private InteractorInput $interactor1,
        private InteractorInput $interactor2,
    ) {}

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

    private function validateStableId(): array
    {
        return strlen($this->stable_id) > 0 && preg_match(self::STABLE_ID_PATTERN, $this->stable_id) === 0
            ? [Error::nested('stable_id', sprintf('must match %s', self::STABLE_ID_PATTERN))]
            : [];
    }

    private function validateMethodId(): array
    {
        return $this->method_id < 1
            ? [Error::nested('method_id', 'must be positive')]
            : [];
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
            ...$this->validateStableIdForDb($pdo),
            ...$this->validateMethodIdForDb($pdo),
            ...$this->interactor1->validateForDbAndType($pdo, $type1)->errors('interactor1'),
            ...$this->interactor2->validateForDbAndType($pdo, $type2)->errors('interactor2'),
        );
    }

    private function validateStableIdForDb(\PDO $pdo): array
    {
        if ($this->stable_id == '') {
            return [];
        }

        $select_descriptions_sth = $pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute([$this->stable_id]);

        $description = $select_descriptions_sth->fetch();

        if (!$description) {
            return [Error::nested('stable_id', 'must exist')];
        }

        if ($description['association_id'] != $this->association_id) {
            return [Error::nested('stable_id', 'must be associated to the same publication')];
        }

        return [];
    }

    private function validateMethodIdForDb(\PDO $pdo): array
    {
        $select_method_sth = $pdo->prepare(self::SELECT_METHOD_SQL);

        $select_method_sth->execute([$this->method_id]);

        $method = $select_method_sth->fetch();

        if (!$method) {
            return [Error::nested('method_id', 'must exist')];
        }

        return [];
    }
}
