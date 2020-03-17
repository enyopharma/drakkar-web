<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Is;
use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\Bound;
use Quanta\Validation\Merged;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\OfType;
use Quanta\Validation\Rules\LessThan;
use Quanta\Validation\Rules\GreaterThan;

final class IsOccurrence
{
    private $subject;

    private $query;

    public function __construct(string $subject, string $query)
    {
        $this->subject = $subject;
        $this->query = $query;
    }

    public function __invoke(array $data): InputInterface
    {
        $coordinatesAreInsideSubject = \Closure::fromCallable([$this, 'coordinatesAreInsideSubject']);
        $coordinatesHaveSameLengthAsQuery = \Closure::fromCallable([$this, 'coordinatesHaveSameLengthAsQuery']);

        $isFlt = new Is(new OfType('float'));
        $isGt96 = new Is(new GreaterThan(96));
        $isLt100 = new Is(new LessThan(100));
        $isCoordinates = new IsCoordinates;
        $areCoordinatesValid = new Is($coordinatesAreInsideSubject, $coordinatesHaveSameLengthAsQuery);

        $validate = new Merged(
            new Bound($isCoordinates, $areCoordinatesValid),
            Field::required('identity', $isFlt, $isGt96, $isLt100),
        );

        return $validate($data);
    }

    private function coordinatesAreInsideSubject(array $coordinates): array
    {
        return $coordinates['stop'] <= strlen($this->subject) ? [] : [
            new Error('occurrence must be inside the subject'),
        ];
    }

    private function coordinatesHaveSameLengthAsQuery(array $coordinates): array
    {
        return $coordinates['stop'] - $coordinates['start'] + 1 == strlen($this->query) ? [] : [
            new Error('occurrence length must be equal to the query length'),
        ];
    }
}
