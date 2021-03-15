<?php

declare(strict_types=1);

namespace App\Routing;

final class MatchingResult
{
    const SUCCESS = 0;

    const FAILURE = 1;

    const NO_VARIANT_MATCHING = 2;

    const PLACEHOLDER_FORMAT_ERROR = 3;

    private int $status;

    private string $path;

    private string $error;

    public static function success(string $path): self
    {
        return new self(self::SUCCESS, $path);
    }

    public static function failure(): self
    {
        return new self(self::FAILURE);
    }

    public static function noVariantMatching(): self
    {
        $error = 'No variant of route \'%%s\' is matching the given placeholders';

        return new self(self::NO_VARIANT_MATCHING, '', $error);
    }

    public static function placeholderFormatError(string $key, string $regex, string $placeholder): self
    {
        $error = vsprintf('Value given for placeholder \'%s\' of route \'%%s\' must match \'%s\', \'%s\' given', [
            $key,
            $regex,
            $placeholder,
        ]);

        return new self(self::PLACEHOLDER_FORMAT_ERROR, '', $error);
    }

    private function __construct(int $status, string $path = '', string $error = '')
    {
        $this->status = $status;
        $this->path = $path;
        $this->error = $error;
    }

    public function isSuccess(): bool
    {
        return $this->status == self::SUCCESS;
    }

    public function isNoVariantMatching(): bool
    {
        return $this->status == self::NO_VARIANT_MATCHING;
    }

    public function isPlaceholderFormatError(): bool
    {
        return $this->status == self::PLACEHOLDER_FORMAT_ERROR;
    }

    public function with(MatchingResult $with): self
    {
        if ($this->isSuccess()) {
            return $with->isSuccess()
                ? self::success($this->path . $with->path)
                : $with;
        }

        return $this;
    }

    public function path(): string
    {
        if ($this->isSuccess()) {
            return $this->path;
        }

        throw new \LogicException('MatchingResult error has no path');
    }

    public function error(string $name): string
    {
        if (!$this->isSuccess()) {
            return sprintf($this->error, $name);
        }

        throw new \LogicException('MatchingResult success has no error');
    }
}
