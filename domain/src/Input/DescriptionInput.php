<?php

declare(strict_types=1);

namespace Domain\Input;

use Quanta\Validation\Bound;
use Quanta\Validation\Success;
use Quanta\Validation\InputInterface;

use Domain\Validations\DataSource;
use Domain\Validations\IsDescription;

final class DescriptionInput
{
    private $data;

    public static function from(\PDO $pdo, string $type, array $data): InputInterface
    {
        $source = new DataSource($pdo);
        $isDescription = new IsDescription($source, $type);
        $factory = fn (array $data) => new Success(new self($data));

        $validate = new Bound($isDescription, $factory);

        return $validate($data);
    }

    private function __construct(array $data)
    {
        $this->data = $data;
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
