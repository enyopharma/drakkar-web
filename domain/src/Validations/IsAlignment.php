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
use Quanta\Validation\Rules\GreaterThan;

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
        $validateAlignment = \Closure::fromCallable([$this, 'validateAlignment']);
        $validateIsoforms = \Closure::fromCallable([$this, 'validateIsoforms']);

        $validate = new Bound($validateAlignment, $validateIsoforms);

        return $validate($data);
    }

    private function validateAlignment(array $data): InputInterface
    {
        $isStr = new Is(new OfType('string'));
        $isArr = new Is(new OfType('array'));
        $isNotEmpty = new Is(new NotEmpty);
        $isSequence = new Is(new GreaterThan(self::MIN_LENGTH), new Matching(self::SEQUENCE_PATTERN));

        $validate = new Merged(
            Field::required('sequence', $isStr, $isNotEmpty, $isSequence),
            Field::required('isoforms', $isArr, new TraverseA($isArr)),
        );

        return $validate($data);
    }

    private function validateIsoforms(array $alignment): InputInterface
    {
        $isIsoform = new IsIsoform(
            $this->source,
            $this->protein,
            $this->start,
            $this->stop,
            $alignment['sequence'],
        );

        $validate = new Merged(
            Field::required('sequence'),
            Field::required('isoforms', new TraverseA($isIsoform))
        );

        return $validate($alignment);
    }
}
