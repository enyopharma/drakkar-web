<?php

declare(strict_types=1);

namespace App\Input\Validation;

interface ErrorFormatterInterface
{
    public function __invoke(Error $error): string;
}
