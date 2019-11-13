<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\HasType;
use Quanta\Validation\Rules\ArrayKey;
use Quanta\Validation\Rules\IsLessThan;
use Quanta\Validation\Rules\IsGreaterThan;

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
        $isflt = new HasType('float');
        $ispos = new IsGreaterThan(0);
        $islt100 = new IsLessThan(100);
        $iscoordinates = new IsCoordinates;
        $arecoordinatesvalid = \Closure::fromCallable([$this, 'areCoordinatesValid']);

        $makeOccurrence = Input::map(fn (array $coordinates, float $identity) => [
            'start' => $coordinates['start'],
            'stop' => $coordinates['stop'],
            'identity' => $identity,
        ]);

        $makeCoordinates = new IsCoordinates;
        $makeIdentity = new ArrayKey('identity', $isflt, $ispos, $islt100);

        return $makeOccurrence(
            $makeCoordinates($data),
            $makeIdentity($data),
        );
    }

    private function areCoordinatesValid(array $coordinates): InputInterface
    {
        $errors = [];

        if ($coordinates['stop'] > strlen($this->subject)) {
            $errors[] = new Error('occurrence must be inside the subject');
        }

        if ($coordinates['stop'] - $coordinates['start'] + 1 != strlen($this->query)) {
            $errors[] = new Error('occurrence length must be equal to the query length');
        }

        return count($errors) == 0 ? Input::unit($coordinates) : new Failure(...$errors);
    }
}
