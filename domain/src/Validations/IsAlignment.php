<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\NestedError;
use Quanta\Validation\InputInterface;

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
        $factory = Input::pure(fn (string $sequence, array ...$isoforms) => compact(
            'sequence', 'isoforms',
        ));

        return Input::unit($data)->validate(
            fn ($x) => $this->step1($factory, $x),
            fn ($x) => $this->step2($factory, $x),
        );
    }

    private function isSequence(string $sequence): InputInterface
    {
        $errors = [];

        if (strlen($sequence) < self::MIN_LENGTH) {
            $errors[] = new Error('%%s => length must be greater than or equal to %s', self::MIN_LENGTH);
        }

        if (preg_match(self::SEQUENCE_PATTERN, $sequence) !== 1) {
            $errors[] = new Error('%%s => must contain only letters');
        }

        return count($errors) == 0 ? new Success($sequence) : new Failure(...$errors);
    }

    private function step1(callable $factory, array $data): InputInterface
    {
        $slice = new Slice;
        $isarr = new IsTypedAs('array');
        $isstr = new IsTypedAs('string');
        $isnotempty = new IsNotEmpty;
        $issequence = \Closure::fromCallable([$this, 'isSequence']);

        $sequence = $slice($data, 'sequence')->validate($isstr, $isnotempty, $issequence);
        $isoforms = $slice($data, 'isoforms')->validate($isarr)->unpack($isarr);

        return $factory($sequence, ...$isoforms);
    }

    private function step2(callable $factory, array $alignment): InputInterface
    {
        $isiso = new IsIsoform(
            $this->source,
            $this->protein,
            $this->start,
            $this->stop,
            $alignment['sequence'],
        );

        $sequence = Input::unit($alignment['sequence']);
        $isoforms = (new Success($alignment['isoforms'], 'isoforms'))->unpack($isiso);

        return $factory($sequence, ...$isoforms);
    }
}
