<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\Error;
use Quanta\Validation\Success;
use Quanta\Validation\Failure;
use Quanta\Validation\InputInterface;

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
        $slice = new Slice;
        $isstr = new IsTypedAs('string');
        $isnotempty = new IsNotEmpty;
        $isaccession = new IsMatching(self::ACCESSION_PATTERN);
        $isprotein = \Closure::fromCallable([$this, 'isProtein']);

        $factory = Input::pure(fn (string $accession) => compact('accession'));

        $accession = $slice($data, 'accession')->validate($isstr, $isnotempty, $isaccession, $isprotein);

        return $factory($accession);
    }

    public function isProtein(string $accession): InputInterface
    {
        return $this->source->protein($accession)
            ? new Success($accession)
            : new Failure(new Error('%%s => no protein with accession %s', $accession));
    }
}
