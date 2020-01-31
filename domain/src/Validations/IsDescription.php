<?php

declare(strict_types=1);

namespace Domain\Validations;

use Quanta\Validation\Is;
use Quanta\Validation\Field;
use Quanta\Validation\Merged;
use Quanta\Validation\InputInterface;
use Quanta\Validation\Rules\OfType;

final class IsDescription
{
    private $association;

    private $source;

    public function __construct(Association $association, DataSource $source)
    {
        $this->association = $association;
        $this->source = $source;
    }

    public function __invoke(array $data): InputInterface
    {
        $isArr = new Is(new OfType('array'));
        $isMethod = new IsMethod($this->source);
        $isInteractor1 = new IsInteractor($this->source, $this->association->type1());
        $isInteractor2 = new IsInteractor($this->source, $this->association->type2());

        $validate = new Merged(
            Field::required('method', $isArr, $isMethod),
            Field::required('interactor1', $isArr, $isInteractor1),
            Field::required('interactor2', $isArr, $isInteractor2),
        );

        return $validate($data);
    }
}
