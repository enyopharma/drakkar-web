<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\HasType;
use Quanta\Validation\Rules\ArrayKeys;
use Quanta\Validation\Rules\IsNotEmpty;
use Quanta\Validation\Rules\IsMatching;

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
        $isstr = new HasType('string');
        $isnotempty = new IsNotEmpty;
        $isaccession = new IsMatching(self::ACCESSION_PATTERN);
        $isexisting = \Closure::fromCallable([$this, 'isExistingAccession']);

        $makeProtein = new ArrayKeys([
            'accession' => [$isstr, $isnotempty, $isaccession, $isexisting],
        ]);

        return $makeProtein($data);
    }

    public function isExistingAccession(string $accession): InputInterface
    {
        return $this->source->protein($accession)
            ? Input::unit($accession)
            : new Failure(new Error('no protein with accession %s', $accession));
    }
}
