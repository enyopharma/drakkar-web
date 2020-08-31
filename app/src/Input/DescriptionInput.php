<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\Field;
use Quanta\Validation\InvalidDataException;
use Quanta\Validation\Rules\OfType;

use App\Assertions\RunType;
use App\Assertions\ProteinType;

final class DescriptionInput
{
    const SELECT_DESCRIPTIONS_SQL = <<<SQL
        SELECT id, association_id FROM descriptions WHERE stable_id = ?
    SQL;

    const SELECT_METHOD_SQL = <<<SQL
        SELECT id FROM methods WHERE id = ?
    SQL;

    private int $association_id;

    private string $stable_id;

    private int $method_id;

    private array $interactor1;

    private array $interactor2;

    public static function factory(\PDO $pdo, int $association_id, string $type): callable
    {
        RunType::argument($type);

        return new Validation(fn (...$xs) => self::from($pdo, $association_id, $type, ...$xs),
            Field::required('stable_id', new Guard(new OfType('string')))->focus(),
            Field::required('method_id', new Guard(new OfType('int')))->focus(),
            Field::required('interactor1', new Guard(new OfType('array')))->focus(),
            Field::required('interactor2', new Guard(new OfType('array')))->focus(),
        );
    }

    public static function from(\PDO $pdo, int $association_id, string $type, string $stable_id, int $method_id, array $interactor1, array $interactor2): self
    {
        RunType::argument($type);

        $input = new self($association_id, $stable_id, $method_id, $interactor1, $interactor2);

        return validated($input, ...$input->validate($pdo, $type));
    }

    private function __construct(int $association_id, string $stable_id, int $method_id, array $interactor1, array $interactor2)
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
            'stable_id' => $this->stable_id,
            'association_id' => $this->association_id,
            'method_id' => $this->method_id,
            'interactor1' => $this->interactor1,
            'interactor2' => $this->interactor2,
        ];
    }

    private function validate(\PDO $pdo, string $type): array
    {
        $type1 = ProteinType::H;
        $type2 = $type == RunType::HH ? ProteinType::H : ProteinType::V;

        return [
            ...$this->validateStableId($pdo),
            ...$this->validateMethod($pdo),
            ...nested('interactor1', ...$this->validateInteractor1($pdo, $type1)),
            ...nested('interactor2', ...$this->validateInteractor2($pdo, $type2)),
        ];
    }

    private function validateStableId(\PDO $pdo): array
    {
        if ($this->stable_id == '') {
            return [];
        }

        $select_descriptions_sth = $pdo->prepare(self::SELECT_DESCRIPTIONS_SQL);

        $select_descriptions_sth->execute([$this->stable_id]);

        $description = $select_descriptions_sth->fetch();

        if (!$description) {
            return [new Error('stable_id must be existing')];
        }

        if ($description['association_id'] != $this->association_id) {
            return [new Error('stable_id must be associated to the same publication')];
        }

        return [];
    }

    private function validateMethod(\PDO $pdo): array
    {
        $select_method_sth = $pdo->prepare(self::SELECT_METHOD_SQL);

        $select_method_sth->execute([$this->method_id]);

        $method = $select_method_sth->fetch();

        return $method ? [] : [new Error('method not found')];
    }

    private function validateInteractor1(\PDO $pdo, string $type): array
    {
        $is_interactor = InteractorInput::factory($pdo, $type);

        try {
            $is_interactor($this->interactor1);
        }

        catch (InvalidDataException $e) {
            return $e->errors();
        }

        return [];
    }

    private function validateInteractor2(\PDO $pdo, string $type): array
    {
        $is_interactor = InteractorInput::factory($pdo, $type);

        try {
            $is_interactor($this->interactor2);
        }

        catch (InvalidDataException $e) {
            return $e->errors();
        }

        return [];
    }
}
