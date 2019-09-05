<?php

declare(strict_types=1);

namespace Domain\Payloads;

final class InputNotValid implements DomainPayloadInterface
{
    private $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function data(): array
    {
        return [];
    }

    public function meta(): array
    {
        return [
            'errors' => $this->errors,
        ];
    }
}
