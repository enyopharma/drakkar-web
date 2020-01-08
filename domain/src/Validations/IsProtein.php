<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Is;
use Quanta\Validation\Error;
use Quanta\Validation\Field;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\OfType;
use Quanta\Validation\Rules\NotEmpty;
use Quanta\Validation\Rules\Matching;

final class IsProtein
{
    const ACCESSION_PATTERN = '/^[A-Z0-9]+$/';

    private $source;

    public function __construct(DataSource $source)
    {
        $this->source = $source;
    }

    public function __invoke(array $data): InputInterface
    {
        $proteinExists = \Closure::fromCallable([$this, 'proteinExists']);

        $isStr = new Is(new OfType('string'));
        $isNotEmpty = new Is(new NotEmpty);
        $isAccession = new Is(new Matching(self::ACCESSION_PATTERN));
        $isProtein = new is($proteinExists);

        $validate = Field::required('accession', $isStr, $isNotEmpty, $isAccession, $isProtein);

        return $validate($data);
    }

    private function proteinExists(string $accession): array
    {
        $protein = $this->source->protein($accession);

        return $protein !== false ? [] : [
            new Error('no protein with accession %s', $accession),
        ];
    }
}
