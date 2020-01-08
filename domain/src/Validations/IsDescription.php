<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Is;
use Quanta\Validation\Field;
use Quanta\Validation\Merged;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\OfType;

use Domain\Run;
use Domain\Protein;

final class IsDescription
{
    private $source;

    private $type;

    public function __construct(DataSource $source, string $type)
    {
        if (! in_array($type, [Run::HH, Run::VH])) {
            throw new \InvalidArgumentException(
                sprintf('type must be either %s or %s, %s given', Run::HH, Run::VH, $type)
            );
        }

        $this->source = $source;
        $this->type = $type;
    }

    public function __invoke(array $data): InputInterface
    {
        $type1 = Protein::H;
        $type2 = $this->type == Run::HH ? Protein::H : Protein::V;

        $isArr = new Is(new OfType('array'));
        $isMethod = new IsMethod($this->source);
        $isInteractor1 = new IsInteractor($this->source, $type1);
        $isInteractor2 = new IsInteractor($this->source, $type2);

        $validate = new Merged(
            Field::required('method', $isArr, $isMethod),
            Field::required('interactor1', $isArr, $isInteractor1),
            Field::required('interactor2', $isArr, $isInteractor2),
        );

        return $validate($data);
    }
}
