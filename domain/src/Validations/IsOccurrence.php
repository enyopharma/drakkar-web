<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

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
        $slice = new Slice;
        $isflt = new IsTypedAs('float');
        $ispos = new IsGreaterThan(0);
        $islt100 = new IsLessThan(100);
        $iscdx = new IsCoordinates;
        $arecdxvalid = \Closure::fromCallable([$this, 'areCdxValid']);

        $factory = Input::pure(fn (array $cdx, float $identity) => [
            'start' => $cdx['start'], 'stop' => $cdx['stop'], 'identity' => $identity,
        ]);

        $cdx = Input::unit($data)->validate($iscdx, $arecdxvalid);
        $identity = $slice($data, 'identity')->validate($isflt, $ispos, $islt100);

        return $factory($cdx, $identity);
    }

    private function areCdxValid(array $cdx): InputInterface
    {
        $errors = [];

        if ($cdx['stop'] > strlen($this->subject)) {
            $errors[] = new Error('%%s => alignment coordinates must be inside the subject');
        }

        if ($cdx['stop'] - $cdx['start'] + 1 != strlen($this->query)) {
            $errors[] = new Error('%%s => alignment coordinates length must be equal to the query length');
        }

        return count($errors) == 0 ? new Success($cdx) : new Failure(...$errors);
    }
}
