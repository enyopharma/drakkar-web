<?php

declare(strict_types=1);

namespace App\Input;

use Quanta\Validation;
use Quanta\Validation\Error;
use Quanta\Validation\Guard;
use Quanta\Validation\Bound;
use Quanta\Validation\Field;
use Quanta\Validation\Rules\OfType;

final class DescriptionInput
{
    private Association $association;

    private MethodInput $method;

    private InteractorInput $interactor1;

    private InteractorInput $interactor2;

    public static function factory(DataSource $source, Association $association): callable
    {
        $factory = fn (...$xs) => new self($association, ...$xs);

        $is_arr = new Guard(new OfType('array'));
        $is_method = MethodInput::factory($source);
        $is_interactor1 = InteractorInput::factory($source);
        $is_interactor2 = InteractorInput::factory($source);
        $are_types_valid = new Guard(fn ($x) => $x->areTypesValid($source, $association));

        $validation = new Validation($factory,
            Field::required('method', $is_arr, $is_method)->focus(),
            Field::required('interactor1', $is_arr, $is_interactor1)->focus(),
            Field::required('interactor2', $is_arr, $is_interactor2)->focus(),
        );

        return new Bound($validation, $are_types_valid);
    }

    private function __construct(
        Association $association,
        MethodInput $method,
        InteractorInput $interactor1,
        InteractorInput $interactor2
    ) {
        $this->association = $association;
        $this->method = $method;
        $this->interactor1 = $interactor1;
        $this->interactor2 = $interactor2;
    }

    private function areTypesValid(DataSource $source, Association $association): array
    {
        $errors = [];

        $type1 = $association->type1();
        $type2 = $association->type2();

        $accession1 = $this->interactor1->protein()->accession();
        $accession2 = $this->interactor2->protein()->accession();

        $protein1 = $source->protein($accession1);
        $protein2 = $source->protein($accession2);

        if ($protein1 && $protein1['type'] != $type1) {
            $errors[] = new Error(sprintf('interactor1 must have type %s', $type1));
        }

        if ($protein2 && $protein2['type'] != $type2) {
            $errors[] = new Error(sprintf('interactor2 must have type %s', $type2));
        }

        return $errors;
    }

    public function data(): array
    {
        return [
            'association' => [
                'id' => $this->association->id(),
            ],
            'method' => $this->method->data(),
            'interactor1' => $this->interactor1->data(),
            'interactor2' => $this->interactor2->data(),
        ];
    }
}
