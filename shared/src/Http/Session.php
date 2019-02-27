<?php declare(strict_types=1);

namespace Shared\Http;

final class Session
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function has(string $key): bool
    {
        return key_exists($key, $this->data);
    }

    public function set(string $key, $value)
    {
        return $this->data[$key] = $value;
    }

    public function get(string $key, $default)
    {
        return $this->data[$key] ?? $default;
    }

    public function unset(string $key)
    {
        unset($this->data[$key]);
    }

    public function data(): array
    {
        return $this->data;
    }
}
