<?php

declare(strict_types=1);

namespace Domain\Input;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

use Domain\Run;
use Domain\Protein;
use Domain\Validations\Slice;
use Domain\Validations\IsMethod;
use Domain\Validations\IsTypedAs;
use Domain\Validations\DataSource;
use Domain\Validations\IsInteractor;

final class DescriptionInput
{
    private $method;
    private $interactor1;
    private $interactor2;

    public static function from(\PDO $pdo, string $type, array $data): InputInterface
    {
        if (! in_array($type, [Run::HH, Run::VH])) {
            throw new \InvalidArgumentException(
                sprintf('type must be either %s or %s, %s given', Run::HH, Run::VH, $type)
            );
        }

        $source = new DataSource($pdo);

        $type1 = Protein::H;
        $type2 = $type == Run::HH ? Protein::H : Protein::V;

        $slice = new Slice;
        $isarr = new IsTypedAs('array');

        $factory = Input::pure(fn (...$xs) => new self(...$xs));

        return $factory(
            $slice($data, 'method')->validate($isarr, new IsMethod($pdo)),
            $slice($data, 'interactor1')->validate($isarr, new IsInteractor($source, $type1)),
            $slice($data, 'interactor2')->validate($isarr, new IsInteractor($source, $type2)),
        );
    }

    private function __construct(array $method, array $interactor1, array $interactor2)
    {
        $this->method = $method;
        $this->interactor1 = $interactor1;
        $this->interactor2 = $interactor2;
    }

    public function method(): array
    {
        return $this->method;
    }

    public function interactor1(): array
    {
        return $this->interactor1;
    }

    public function interactor2(): array
    {
        return $this->interactor2;
    }
}
