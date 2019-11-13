<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\HasType;
use Quanta\Validation\Rules\ArrayShape;

use Domain\Run;
use Domain\Protein;

final class IsDescription
{
    private $pdo;

    private $type;

    public function __construct(\PDO $pdo, string $type)
    {
        if (! in_array($type, [Run::HH, Run::VH])) {
            throw new \InvalidArgumentException(
                sprintf('type must be either %s or %s, %s given', Run::HH, Run::VH, $type)
            );
        }

        $this->pdo = $pdo;
        $this->type = $type;
    }

    public function __invoke(array $data): InputInterface
    {
        $source = new DataSource($this->pdo);

        $type1 = Protein::H;
        $type2 = $this->type == Run::HH ? Protein::H : Protein::V;

        $isarr = new HasType('array');

        $makeDescription = new ArrayShape([
            'method' => [$isarr, new IsMethod($this->pdo)],
            'interactor1' => [$isarr, new IsInteractor($source, $type1)],
            'interactor2' => [$isarr, new IsInteractor($source, $type2)],
        ]);

        return $makeDescription($data);
    }
}
