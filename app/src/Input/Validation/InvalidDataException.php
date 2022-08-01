<?php

declare(strict_types=1);

namespace App\Input\Validation;

final class InvalidDataException extends \DomainException
{
    public static function error(string $template, mixed ...$xs): self
    {
        return new self(Error::from($template, ...$xs));
    }

    /**
     * @var \App\Input\Validation\Error[]
     */
    public readonly array $errors;

    public function __construct(Error $error, Error ...$errors)
    {
        $this->errors = [$error, ...$errors];

        parent::__construct('invalid data');
    }
}
