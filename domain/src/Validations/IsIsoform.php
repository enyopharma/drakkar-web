<?php

declare(strict_types=1);

namespace Domain\Validations;

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
        $validateIsoform = \Closure::fromCallable([$this, 'validateIsoform']);
        $validateOccurrences = \Closure::fromCallable([$this, 'validateOccurrences']);

        $validate = new Bound($validateIsoform, $validateOccurrences);

        return $validate($data);
    }

    private function validateIsoform(array $data): InputInterface
    {
        $sequenceExists = \Closure::fromCallable([$this, 'sequenceExists']);
        $isoformExists = \Closure::fromCallable([$this, 'isoformExists']);

        $isStr = new Is(new OfType('string'));
        $isArr = new Is(new OfType('array'));
        $isNotEmpty = new Is(new NotEmpty);
        $isAccession = new Is(new Matching(self::ACCESSION_PATTERN));
        $isSequence = new Is($sequenceExists);
        $isIsoform = new Is($isoformExists);

        $validate = new Merged(
            Field::required('accession', $isStr, $isNotEmpty, $isAccession, $isSequence, $isIsoform),
            Field::required('occurrences', $isArr, new TraverseA($isArr)),
        );

        return $validate($data);
    }

    private function validateOccurrences(array $isoform): InputInterface
    {
        $data = $this->source->sequence($isoform['accession']);

        if (! $data) {
            throw new \LogicException;
        }

        $subject = $data['is_canonical']
            ? substr($data['sequence'], $this->start, (int) ($this->stop - $this->start + 1))
            : $data['sequence'];

        $isOccurrence = new IsOccurrence($subject, $this->query);

        $validate = new Merged(
            Field::required('accession'),
            Field::required('occurrences', new TraverseA($isOccurrence))
        );

        return $validate($isoform);
    }

    private function sequenceExists(string $accession): array
    {
        $data = $this->source->sequence($accession);

        return $data !== false ? [] : [
            new Error(sprintf('no sequence with accession %s', $accession)),
        ];
    }

    private function isoformExists(string $accession): array
    {
        $data = $this->source->sequence($accession);

        if (! $data) {
            throw new \LogicException;
        }

        return $data['protein'] == $this->protein ? [] : [
            new Error(sprintf('protein with accession %s has no isoform with accession %s', $this->protein, $accession)),
        ];
    }

}
