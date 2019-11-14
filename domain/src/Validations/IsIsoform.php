<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\HasType;
use Quanta\Validation\Rules\ArrayKey;
use Quanta\Validation\Rules\ArrayKeys;
use Quanta\Validation\Rules\IsNotEmpty;
use Quanta\Validation\Rules\IsMatching;

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
        return Input::unit($data)->bind(
            fn ($x) => $this->makeIsoform($x),
            fn ($x) => $this->validateOccurrences($x),
        );
    }

    private function isSequence(string $accession): InputInterface
    {
        return $this->source->sequence($accession)
            ? Input::unit($accession)
            : new Failure(new Error(
                sprintf('no sequence with accession %s', $accession)
            ));
    }

    private function isIsoform(string $accession): InputInterface
    {
        $data = $this->source->sequence($accession);

        return $data['protein'] == $this->protein
            ? Input::unit($accession)
            : new Failure(new Error(
                sprintf('protein with accession %s has no isoform with accession %s', $this->protein, $accession)
            ));
    }

    private function makeIsoform(array $data): InputInterface
    {
        $isarr = new HasType('array');
        $isstr = new HasType('string');
        $isnotempty = new IsNotEmpty;
        $isaccession = new IsMatching(self::ACCESSION_PATTERN);
        $issequence = \Closure::fromCallable([$this, 'isSequence']);
        $isisoform = \Closure::fromCallable([$this, 'isIsoform']);

        $makeIsoform = new ArrayKeys([
            'accession' => [$isstr, $isnotempty, $isaccession, $issequence, $isisoform],
            'occurrences' => [$isarr, Input::traverseA($isarr)],
        ]);

        return $makeIsoform($data);
    }

    private function validateOccurrences(array $isoform): InputInterface
    {
        $data = $this->source->sequence($isoform['accession']);

        $subject = $data['is_canonical']
            ? substr($data['sequence'], $this->start, (int) ($this->stop - $this->start + 1))
            : $data['sequence'];

        $validate = new ArrayKey('occurrences', Input::traverseA(new IsOccurrence(
            $subject,
            $this->query,
        )));

        return $validate($isoform)->bind(fn () => Input::unit($isoform));
    }
}
