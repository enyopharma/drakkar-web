<?php

declare(strict_types=1);

namespace App\Input\Validation;

final class Result
{
    const SUCCESS = 1;
    const FINAL = 2;
    const ERROR = 3;

    /**
     * Return a successful result containing the given value.
     */
    public static function success(mixed $value): self
    {
        return new self(self::SUCCESS, [$value]);
    }

    /**
     * Return a successful result containing the given value and short-circuiting
     * subsequent validations.
     */
    public static function final(mixed $value): self
    {
        return new self(self::FINAL, [$value]);
    }

    /**
     * Return an error result creating a single error from the given template and variables.
     */
    public static function error(string $template, mixed ...$xs): self
    {
        return self::errors(Error::from($template, ...$xs));
    }

    /**
     * Return an error result containing the given errors.
     */
    public static function errors(Error $error, Error ...$errors): self
    {
        return new self(self::ERROR, [], [$error, ...$errors]);
    }

    /**
     * Turn a factory into a validation function.
     *
     * (a -> b -> c -> ...) -> (Result<a> -> Rersult<b> -> Result<c> -> ...)
     */
    public static function liftn(callable $f): callable
    {
        return function (self ...$results) use ($f): self {
            $result = self::merge(...$results);

            return match ($result->status) {
                self::SUCCESS => self::result($f, ...$result->values),
                self::ERROR => $result,
                self::FINAL => throw new \Exception, // should never happen
            };
        };
    }

    /**
     * Turn a validation function into a composable validation function.
     *
     * (a -> Result<b>) -> (Result<a> -> Result<b>)
     */
    public static function bind(callable $f): callable
    {
        return function (self $result) use ($f): self {
            return match ($result->status) {
                self::SUCCESS => self::result($f, ...$result->values),
                self::ERROR, self::FINAL => $result,
            };
        };
    }

    /**
     * Merge many Result instances together by accumulating values or accumulatig errors.
     *
     * - When no Result given empty successful result is returned.
     * - When one Result given it is returned.
     * - When only success Result are given the final Result is a success accumulating values.
     * - When one error Result is given the final Result is an error accumulating all errors.
     *
     * Merged successful results can have zero or many values because the primary goal of merging
     * results is to use it as an argument list for a factory. Maybe this strange behavior can be
     * removed by implementing apply/currying.
     */
    private static function merge(Result ...$results): self
    {
        if (count($results) === 0) return new self(self::SUCCESS);

        $result1 = array_shift($results);

        if (count($results) === 0) return $result1;

        $result2 = array_shift($results);

        $result = match ($result1->status) {
            self::SUCCESS, self::FINAL => match ($result2->status) {
                self::SUCCESS, self::FINAL => new self(self::SUCCESS, [...$result1->values, ...$result2->values]),
                self::ERROR => $result2,
            },
            self::ERROR => match ($result2->status) {
                self::SUCCESS, self::FINAL => $result1,
                self::ERROR => self::errors(...$result1->errors, ...$result2->errors),
            },
        };

        return self::merge($result, ...$results);
    }

    /**
     * Execute the given function with the given parameters so it always return a result whether it
     * returns any value or throw an InvalidDataException.
     *
     * It allows any object factory to be used as a validation function.
     *
     * @param callable(mixed ...$xs): mixed ...$xs
     */
    public static function result(callable $f, mixed ...$xs): self
    {
        try {
            $value = $f(...$xs);
        } catch (InvalidDataException $e) {
            return self::errors(...$e->errors);
        }

        if (!$value instanceof self) {
            return self::success($value);
        }

        return $value;
    }

    /**
     * @param 1|2|3                         $status
     * @param mixed[]                       $values
     * @param \App\Input\Validation\Error[] $errors
     */
    private function __construct(
        private int $status,
        private array $values = [],
        private array $errors = [],
    ) {
    }

    /**
     * Return the value of a successful Result or throw an InvalidDataException when the
     * result is an error.
     */
    public function value(): mixed
    {
        return match ($this->status) {
            self::SUCCESS, self::FINAL => $this->values[0] ?? throw new \Exception, // should never throw
            self::ERROR => throw new InvalidDataException(...$this->errors),
        };
    }

    /**
     * When the result is an error, nest them within the given keys.
     */
    public function nest(string $key, string ...$keys): self
    {
        if ($this->status == self::ERROR) {
            $errors = array_map(fn ($e) => $e->nest($key, ...$keys), $this->errors);

            return self::errors(...$errors);
        }

        return $this;
    }

    /**
     * Sequentially apply the given composable validation functions to this result.
     */
    public function reduce(callable ...$fs): Result
    {
        if (count($fs) == 0) return $this;

        $f = array_shift($fs);

        return $f($this)->reduce(...$fs);
    }
}
