<?php

declare(strict_types=1);

namespace Domain\Input;

use Quanta\Validation\Bound;
use Quanta\Validation\Success;
use Quanta\Validation\InputInterface;

use Domain\Validations\DataSource;
use Domain\Validations\Association;
use Domain\Validations\IsDescription;

final class DescriptionInput
{
    private $association;

    private $data;

    public static function from(\PDO $pdo, Association $association, array $data): InputInterface
    {
        $validate = new IsDescription($association, new DataSource($pdo));

        return $validate($data)->bind(function (array $data) use ($association) {
            return new Success(new self($association, $data));
        });
    }

    private function __construct(Association $association, array $data)
    {
        $this->association = $association;
        $this->data = $data;
    }

    public function association(): Association
    {
        return $this->association;
    }

    public function method(): array
    {
        return $this->data['method'];
    }

    public function interactor1(): array
    {
        return $this->data['interactor1'];
    }

    public function interactor2(): array
    {
        return $this->data['interactor2'];
    }
}
