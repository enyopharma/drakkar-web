<?php

declare(strict_types=1);

namespace App\Input\Validation;

use App\Input\Validation\Common;

final class ArrayKey
{
    /**
     * Return a new required ArrayKey.
     */
    public static function required(string $key): self
    {
        return new self($key, true, null);
    }

    /**
     * Return a new optional ArrayKey with the given default value.
     */
    public static function optional(string $key, mixed $default = null): self
    {
        return new self($key, false, $default);
    }

    /**
     * @var Array<callable(mixed): \App\Input\Validation\Result>
     */
    private array $validations;

    private function __construct(
        private string $key,
        private bool $required,
        private mixed $default,
        callable ...$validations
    ) {
        $this->validations = $validations;
    }

    public function int(callable ...$validations): self
    {
        return $this->then(new Common\IsInt, ...$validations);
    }

    public function float(callable ...$validations): self
    {
        return $this->then(new Common\IsFloat, ...$validations);
    }

    public function string(callable ...$validations): self
    {
        return $this->then(new Common\IsString, ...$validations);
    }

    public function array(callable ...$validations): self
    {
        return $this->then(new Common\IsArray, ...$validations);
    }

    /**
     * Return a new ArrayKey with the given validation function added.
     *
     * Bind is applied on each validation function so they are now composable.
     *
     * @param callable(mixed): \App\Input\Validation\Result ...$validations
     */
    public function then(callable ...$validations): self
    {
        if (count($validations) == 0) return $this;

        $validation = Result::bind(array_shift($validations));

        $new = new self($this->key, $this->required, $this->default, ...$this->validations, ...[$validation]);

        return $new->then(...$validations);
    }

    /**
     * Get a Result by applying required/optional validation to the given array then sequentially
     * apply each validation function on it.
     *
     * @param mixed[] $data
     */
    public function __invoke(array $data): Result
    {
        return Composition::from(...$this->validations)
            ->reduce($this->result($data))
            ->nest($this->key);
    }

    /**
     * @param mixed[] $data
     */
    private function result(array $data): Result
    {
        if (array_key_exists($this->key, $data)) {
            return Result::success($data[$this->key]);
        }

        if (!$this->required) {
            return Result::final($this->default);
        }

        return Result::error('%%s is required');
    }
}
