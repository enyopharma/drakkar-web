<?php

declare(strict_types=1);

namespace Domain\Validations;

use Domain\Protein;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\HasType;
use Quanta\Validation\Rules\ArrayKey;
use Quanta\Validation\Rules\IsNotEmpty;
use Quanta\Validation\Rules\IsMatching;

final class IsInteractor
{
    const NAME_PATTERN = '/^[^\s]+$/';

    private $source;

    private $type;

    public function __construct(DataSource $source, string $type)
    {
        $this->source = $source;
        $this->type = $type;
    }

    public function __invoke(array $data): InputInterface
    {
        return Input::unit($data)->bind(
            fn ($x) => $this->makeInteractor($x),
            fn ($x) => $this->validateCoordinates($x),
            fn ($x) => $this->isNameConsistent($x),
            fn ($x) => $this->areCoordinatesConsistent($x),
            fn ($x) => $this->validateAlignments($x),
        );
    }

    private function isTypeValid(array $protein): InputInterface
    {
        $data = $this->source->protein($protein['accession']);

        return $data['type'] == $this->type
            ? Input::unit($protein)
            : new Failure(new Error('protein must have type %s', $this->type));
    }

    private function makeInteractor(array $data): InputInterface
    {
        $isarr = new HasType('array');
        $isstr = new HasType('string');
        $isnotempty = new IsNotEmpty;
        $isname = new IsMatching(self::NAME_PATTERN);
        $isprotein = new IsProtein($this->source);
        $istypevalid = \Closure::fromCallable([$this, 'isTypeValid']);

        $makeInteractor = Input::map(fn (array $protein, string $name, array $coordinates, array $mapping) => [
            'protein' => $protein,
            'name' => $name,
            'start' => $coordinates['start'],
            'stop' => $coordinates['stop'],
            'mapping' => $mapping,
        ]);

        $makeProtein = new ArrayKey('protein', $isprotein, $istypevalid);
        $makeName = new ArrayKey('name', $isstr, $isnotempty, $isname);
        $makeCoordinates = new IsCoordinates;
        $makeMapping = new ArrayKey('mapping', $isarr, Input::traverseA($isarr));

        return $makeInteractor(
            $makeProtein($data),
            $makeName($data),
            $makeCoordinates($data),
            $makeMapping($data),
        );
    }

    private function validateCoordinates(array $interactor): InputInterface
    {
        $data = $this->source->protein($interactor['protein']['accession']);

        $length = strlen($data['sequence']);

        if ($data['type'] == Protein::H && $interactor['stop'] != $length) {
            return new Failure(new Error('human interactor must be full length'));
        }

        if ($interactor['stop'] > $length) {
            return new Failure(new Error('interactor coordinates must be inside the protein'));
        }

        return Input::unit($interactor);
    }

    private function isNameConsistent(array $interactor): InputInterface
    {
        $data = $this->source->name(
            $interactor['protein']['accession'],
            $interactor['start'],
            $interactor['stop'],
        );

        return ! $data || $interactor['name'] == $data['name']
            ? Input::unit($interactor)
            : new Failure(new Error(
                vsprintf('invalid name %s for interactor (%s, %s, %s) - %s expected', [
                    $interactor['name'],
                    $interactor['protein']['accession'],
                    $interactor['start'],
                    $interactor['stop'],
                    $data['name'],
                ])));
    }

    private function areCoordinatesConsistent(array $interactor): InputInterface
    {
        $data = $this->source->coordinates(
            $interactor['protein']['accession'],
            $interactor['name'],
        );

        return ! $data || ($interactor['start'] == $data['start'] && $interactor['stop'] == $data['stop'])
            ? Input::unit($interactor)
            : new Failure(new Error(
                vsprintf('invalid coordinates [%s - %s] for interactor (%s, %s) - [%s - %s] expected', [
                    $interactor['start'],
                    $interactor['stop'],
                    $interactor['protein']['accession'],
                    $interactor['name'],
                    $data['start'],
                    $data['stop'],
                ])));
    }

    private function validateAlignments(array $interactor): InputInterface
    {
        $validate = new ArrayKey('mapping', Input::traverseA(new IsAlignment(
            $this->source,
            $interactor['protein']['accession'],
            $interactor['start'],
            $interactor['stop'],
        )));

        return $validate($interactor)->bind(fn () => Input::unit($interactor));
    }
}
