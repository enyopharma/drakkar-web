<?php declare(strict_types=1);

namespace App\Domain;

final class DomainSuccess implements DomainPayloadInterface
{
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function parsed(callable $success, array $errors = [])
    {
        return $success($this->data);
    }
}
