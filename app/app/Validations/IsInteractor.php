<?php

declare(strict_types=1);

namespace App\Validations;

use Quanta\Validation\Is;
use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\Bound;
use Quanta\Validation\Merged;
use Quanta\Validation\TraverseA;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\OfType;
use Quanta\Validation\Rules\NotEmpty;
use Quanta\Validation\Rules\Matching;

final class IsInteractor
{
    const NAME_PATTERN = '/^[^\s]+$/';

    private DataSource $source;

    private string $type;

    public function __construct(DataSource $source, string $type)
    {
        $this->source = $source;
        $this->type = $type;
    }

    public function __invoke(array $data): InputInterface
    {
        $validateInteractor = \Closure::fromCallable([$this, 'validateInteractor']);
        $coordinatesAreValid = \Closure::fromCallable([$this, 'coordinatesAreValid']);
        $nameIsConsistent = \Closure::fromCallable([$this, 'nameIsConsistent']);
        $coordinatesAreConsistent = \Closure::fromCallable([$this, 'coordinatesAreConsistent']);
        $validateAlignments = \Closure::fromCallable([$this, 'validateAlignments']);

        $validateCoordinates = new Is($coordinatesAreValid);
        $validateNameConsistency = new Is($nameIsConsistent);
        $validateCoordinatesConsistency = new Is($coordinatesAreConsistent);

        $validate = new Bound(
            $validateInteractor,
            $validateCoordinates,
            $validateNameConsistency,
            $validateCoordinatesConsistency,
            $validateAlignments,
        );

        return $validate($data);
    }

    private function validateInteractor(array $data): InputInterface
    {
        $typeIsValid = \Closure::fromCallable([$this, 'typeIsValid']);

        $isStr = new Is(new OfType('string'));
        $isArr = new Is(new OfType('array'));
        $isNotEmpty = new Is(new NotEmpty);
        $isName = new Is(new Matching(self::NAME_PATTERN));
        $isProtein = new IsProtein($this->source);
        $isTypeValid = new Is($typeIsValid);
        $isCoordinates = new IsCoordinates;

        $validate = new Merged(
            Field::required('protein', $isProtein, $isTypeValid),
            Field::required('name', $isStr, $isNotEmpty, $isName),
            $isCoordinates,
            Field::required('mapping', $isArr, new TraverseA($isArr)),
        );

        return $validate($data);
    }

    private function validateAlignments(array $interactor): InputInterface
    {
        $isAlignment = new IsAlignment(
            $this->source,
            $interactor['protein']['accession'],
            $interactor['start'],
            $interactor['stop'],
        );

        $validate = new Merged(
            Field::required('protein'),
            Field::required('name'),
            Field::required('start'),
            Field::required('stop'),
            Field::required('mapping', new TraverseA($isAlignment)),
        );

        return $validate($interactor);
    }

    private function typeIsValid(array $protein): array
    {
        $data = $this->source->protein($protein['accession']);

        if (! $data) {
            throw new \LogicException;
        }

        return $data['type'] == $this->type ? [] : [
            new Error('protein must have type %s', $this->type),
        ];
    }

    private function coordinatesAreValid(array $interactor): array
    {
        $data = $this->source->protein($interactor['protein']['accession']);

        if (! $data) {
            throw new \LogicException;
        }

        $length = strlen($data['sequence']);

        if ($data['type'] == 'h' && $interactor['stop'] != $length) {
            return [new Error('human interactor must be full length')];
        }

        if ($interactor['stop'] > $length) {
            return [new Error('interactor coordinates must be inside the protein')];
        }

        return [];
    }

    private function nameIsConsistent(array $interactor): array
    {
        $data = $this->source->name(
            $interactor['protein']['accession'],
            $interactor['start'],
            $interactor['stop'],
        );

        return ! $data || $interactor['name'] == $data['name'] ? [] : [
            new Error(
                vsprintf('invalid name %s for interactor (%s, %s, %s) - %s expected', [
                    $interactor['name'],
                    $interactor['protein']['accession'],
                    $interactor['start'],
                    $interactor['stop'],
                    $data['name'],
                ])
            )
        ];
    }

    private function coordinatesAreConsistent(array $interactor): array
    {
        $data = $this->source->coordinates(
            $interactor['protein']['accession'],
            $interactor['name'],
        );

        return ! $data || ($interactor['start'] == $data['start'] && $interactor['stop'] == $data['stop']) ? [] : [
            new Error(
                vsprintf('invalid coordinates [%s - %s] for interactor (%s, %s) - [%s - %s] expected', [
                    $interactor['start'],
                    $interactor['stop'],
                    $interactor['protein']['accession'],
                    $interactor['name'],
                    $data['start'],
                    $data['stop'],
                ])
            )
        ];
    }
}
