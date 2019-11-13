<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\HasType;
use Quanta\Validation\Rules\ArrayKey;
use Quanta\Validation\Rules\ArrayShape;
use Quanta\Validation\Rules\IsNotEmpty;

final class IsAlignment
{
    const MIN_LENGTH = 4;

    const SEQUENCE_PATTERN = '/^[a-zA-Z]*$/';

    private $source;

    private $protein;

    private $start;

    private $stop;

    public function __construct(DataSource $source, string $protein, int $start, int $stop)
    {
        $this->source = $source;
        $this->protein = $protein;
        $this->start = $start;
        $this->stop = $stop;
    }

    public function __invoke(array $data): InputInterface
    {
        return Input::unit($data)->bind(
            fn ($x) => $this->makeAlignment($x),
            fn ($x) => $this->validateIsoforms($x),
        );
    }

    private function isSequence(string $sequence): InputInterface
    {
        $errors = [];

        if (strlen($sequence) < self::MIN_LENGTH) {
            $errors[] = new Error(sprintf('length must be greater than or equal to %s', self::MIN_LENGTH));
        }

        if (preg_match(self::SEQUENCE_PATTERN, $sequence) !== 1) {
            $errors[] = new Error('must contain only letters');
        }

        return count($errors) == 0 ? Input::unit($sequence) : new Failure(...$errors);
    }

    private function makeAlignment(array $data): InputInterface
    {
        $isarr = new HasType('array');
        $isstr = new HasType('string');
        $isnotempty = new IsNotEmpty;
        $issequence = \Closure::fromCallable([$this, 'isSequence']);

        $makeAlignment = new ArrayShape([
            'sequence' => [$isstr, $isnotempty, $issequence],
            'isoforms' => [$isarr, Input::traverseA($isarr)],
        ]);

        return $makeAlignment($data);
    }

    private function validateIsoforms(array $alignment): InputInterface
    {
        $validate = new ArrayKey('isoforms', Input::traverseA(new IsIsoform(
            $this->source,
            $this->protein,
            $this->start,
            $this->stop,
            $alignment['sequence'],
        )));

        return $validate($alignment)->bind(fn () => Input::unit($alignment));
    }
}
