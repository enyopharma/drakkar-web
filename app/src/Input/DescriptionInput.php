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
    const SELECT_METHOD_SQL = <<<SQL
        SELECT id FROM methods WHERE id = ?
    SQL;

    private int $association_id;

    private int $method_id;

    private array $interactor1;

    private array $interactor2;

    public static function factory(\PDO $pdo, int $association_id, string $type): callable
    {
        RunType::argument($type);

        return new Validation(fn (...$xs) => self::from($pdo, $association_id, $type, ...$xs),
            Field::required('method_id', new Guard(new OfType('int')))->focus(),
            Field::required('interactor1', new Guard(new OfType('array')))->focus(),
            Field::required('interactor2', new Guard(new OfType('array')))->focus(),
        );
    }

    public static function from(\PDO $pdo, int $association_id, string $type, int $method_id, array $interactor1, array $interactor2): self
    {
        RunType::argument($type);

        $type1 = ProteinType::H;
        $type2 = $type == RunType::HH ? ProteinType::H : ProteinType::V;

        $input = new self($association_id, $method_id, $interactor1, $interactor2);

        $errors = [
            ...$input->validateMethod($pdo),
            ...array_map(fn ($x) => $x->nest('interactor1'), $input->validateInteractor1($pdo, $type1)),
            ...array_map(fn ($x) => $x->nest('interactor2'), $input->validateInteractor2($pdo, $type2)),
        ];

        if (count($errors) > 0) {
            throw new InvalidDataException(...$errors);
        }

        return $input;
    }

    private function __construct(int $association_id, int $method_id, array $interactor1, array $interactor2)
    {
        $this->association_id = $association_id;
        $this->method_id = $method_id;
        $this->interactor1 = $interactor1;
        $this->interactor2 = $interactor2;
    }

    public function data(): array
    {
        return [
            'association_id' => $this->association_id,
            'method_id' => $this->method_id,
            'interactor1' => $this->interactor1,
            'interactor2' => $this->interactor2,
        ];
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
