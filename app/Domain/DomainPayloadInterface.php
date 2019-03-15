<?php declare(strict_types=1);

namespace App\Domain;

interface DomainPayloadInterface
{
    public function parsed(callable $success, array $errors = []);
}
