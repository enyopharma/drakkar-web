<?php

declare(strict_types=1);

namespace Domain\Input;

use Quanta\Validation\Input;
use Quanta\Validation\InputInterface;

use Domain\Validations\IsDescription;

final class DescriptionInput
{
    private $data;

    public static function from(\PDO $pdo, string $type, array $data): InputInterface
    {
        $makeDescription = new IsDescription($pdo, $type);
        $makeInstance = fn (array $data) => Input::unit(new self($data));

        return $makeDescription($data)->bind($makeInstance);
    }

    private function __construct(array $data)
    {
        $this->data['method'] = $data['method'];
        $this->data['interactor1'] = $data['interactor1'];
        $this->data['interactor2'] = $data['interactor2'];
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
