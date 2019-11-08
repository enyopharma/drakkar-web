<?php

declare(strict_types=1);

namespace Domain\Validations;

use Domain\Protein;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

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
        $factory = Input::pure(fn (array $protein, string $name, array $cdx, array ...$mapping) => [
            'protein' => $protein,
            'name' => $name,
            'start' => $cdx['start'],
            'stop' => $cdx['stop'],
            'mapping' => $mapping,
        ]);

        return Input::unit($data)->validate(
            fn ($x) => $this->step1($factory, $x),
            fn ($x) => $this->step2($factory, $x),
            fn ($x) => $this->step3($factory, $x),
            fn ($x) => $this->step4($factory, $x),
        );
    }

    private function isTypeValid(array $protein): InputInterface
    {
        $data = $this->source->protein($protein['accession']);

        return $data['type'] == $this->type
            ? new Success($protein)
            : new Failure(new Error('%%s => protein must have type %s', $this->type));
    }

    private function areCdxValid(array $interactor): InputInterface
    {
        $data = $this->source->protein($interactor['protein']['accession']);

        $length = strlen($data['sequence']);

        if ($data['type'] == Protein::H && $interactor['stop'] != $length) {
            return new Failure(new Error('%%s => human interactor must be full length'));
        }

        if ($interactor['stop'] > $length) {
            return new Failure(new Error('%%s => interactor coordinates must be inside the protein'));
        }

        return new Success($interactor);
    }

    private function isNameConsistent(array $interactor): InputInterface
    {
        $data = $this->source->name(
            $interactor['protein']['accession'],
            $interactor['start'],
            $interactor['stop'],
        );

        return ! $data || $interactor['name'] == $data['name']
            ? new Success($interactor)
            : new Failure(new Error(
                '%%s => invalid name %s for interactor (%s, %s, %s) - %s expected',
                $interactor['name'],
                $interactor['protein']['accession'],
                $interactor['start'],
                $interactor['stop'],
                $data['name'],
            ));
    }

    private function areCdxConsistent(array $interactor): InputInterface
    {
        $data = $this->source->coordinates(
            $interactor['protein']['accession'],
            $interactor['name'],
        );

        return ! $data || ($interactor['start'] == $data['start'] && $interactor['stop'] == $data['stop'])
            ? new Success($interactor)
            : new Failure(new Error(
                '%%s => invalid coordinates [%s - %s] for interactor (%s, %s) - [%s - %s] expected',
                $interactor['start'],
                $interactor['stop'],
                $interactor['protein']['accession'],
                $interactor['name'],
                $data['start'],
                $data['stop'],
            ));
    }

    private function step1(callable $factory, array $data): InputInterface
    {
        $slice = new Slice;
        $isarr = new IsTypedAs('array');
        $isstr = new IsTypedAs('string');
        $onkey = fn ($x) => new OnKey($x);
        $isnotempty = new IsNotEmpty;
        $isname = new IsMatching(self::NAME_PATTERN);
        $isprotein = new IsProtein($this->source);
        $istypevalid = \Closure::fromCallable([$this, 'isTypeValid']);
        $iscdx = new IsCoordinates;

        $protein = $slice($data, 'protein')->validate($isprotein, $istypevalid);
        $name = $slice($data, 'name')->validate($isstr, $isnotempty, $isname);
        $cdx = Input::unit($data)->validate($iscdx);
        $mapping = $slice($data, 'mapping')->unpack($isarr);

        return $factory($protein, $name, $cdx, ...$mapping);
    }

    private function step2(callable $factory, array $interactor): InputInterface
    {
        $arecdxvalid = \Closure::fromCallable([$this, 'areCdxValid']);

        return Input::unit($interactor)->validate($arecdxvalid);
    }

    private function step3(callable $factory, array $interactor): InputInterface
    {
        $isnameconsistent = \Closure::fromCallable([$this, 'isNameConsistent']);
        $arecdxconsistent = \Closure::fromCallable([$this, 'areCdxConsistent']);

        return Input::unit($interactor)->validate($isnameconsistent, $arecdxconsistent);
    }

    private function step4(callable $factory, array $interactor): InputInterface
    {
        $isalignment = new IsAlignment(
            $this->source,
            $interactor['protein']['accession'],
            $interactor['start'],
            $interactor['stop'],
        );

        $protein = Input::unit($interactor['protein']);
        $name = Input::unit($interactor['name']);
        $cdx = Input::unit($interactor);
        $mapping = (new Success($interactor['mapping'], 'mapping'))->unpack($isalignment);

        return $factory($protein, $name, $cdx, ...$mapping);
    }
}
