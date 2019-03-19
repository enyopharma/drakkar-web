<?php declare(strict_types=1);

namespace App\Domain;

final class DomainPayload implements DomainPayloadInterface
{
    private $status;

    private $data;

    public function __construct(int $status, array $data = [])
    {
        $this->status = $status;
        $this->data = $data;
    }

    public function parsed(callable $success, array $errors = [])
    {
        if (key_exists($this->status, $errors)) {
            return $errors[$this->status]($this->data);
        }

        throw new \LogicException;
    }
}
