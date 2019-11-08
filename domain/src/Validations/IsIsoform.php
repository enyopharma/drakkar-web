<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\NestedError;
use Quanta\Validation\InputInterface;

final class IsIsoform
{
    const ACCESSION_PATTERN = '/^[A-Z0-9]+(-[0-9]+)?$/';

    private $source;

    private $protein;

    private $start;

    private $stop;

    private $query;

    public function __construct(DataSource $source, string $protein, int $start, int $stop, string $query)
    {
        $this->source = $source;
        $this->protein = $protein;
        $this->start = $start;
        $this->stop = $stop;
        $this->query = $query;
    }

    public function __invoke(array $data): InputInterface
    {
        $factory = Input::pure(fn (string $accession, array ...$occurrences) => compact(
            'accession', 'occurrences',
        ));

        return Input::unit($data)->validate(
            fn ($x) => $this->step1($factory, $x),
            fn ($x) => $this->step2($factory, $x),
        );
    }

    private function isSequence(string $accession): InputInterface
    {
        return $this->source->sequence($accession)
            ? new Success($accession)
            : new Failure(new Error('%%s => no sequence with accession %s', $accession));
    }

    private function isIsoform(string $accession): InputInterface
    {
        $data = $this->source->sequence($accession);

        return $data['protein'] == $this->protein
            ? new Success($accession)
            : new Failure(new Error(
                '%%s => protein with accession %s has no isoform with accession %s',
                $this->protein,
                $accession,
            ));
    }

    private function step1(callable $factory, array $data): InputInterface
    {
        $slice = new Slice;
        $isarr = new IsTypedAs('array');
        $isstr = new IsTypedAs('string');
        $isnotempty = new IsNotEmpty;
        $isaccession = new IsMatching(self::ACCESSION_PATTERN);
        $isseq = \Closure::fromCallable([$this, 'isSequence']);
        $isiso = \Closure::fromCallable([$this, 'isIsoform']);

        $accession = $slice($data, 'accession')->validate($isstr, $isnotempty, $isaccession, $isseq, $isiso);
        $occurrences = $slice($data, 'occurrences')->validate($isarr)->unpack($isarr);

        return $factory($accession, ...$occurrences);
    }

    private function step2(callable $factory, array $isoform): InputInterface
    {
        $data = $this->source->sequence($isoform['accession']);

        $subject = $data['is_canonical']
            ? substr($data['sequence'], $this->start, (int) ($this->stop - $this->start + 1))
            : $data['sequence'];

        $isocc = new IsOccurrence($subject, $this->query);

        $accession = Input::unit($isoform['accession']);
        $occurrences = (new Success($isoform['occurrences'], 'occurrences'))->unpack($isocc);

        return $factory($accession, ...$occurrences);
    }
}
