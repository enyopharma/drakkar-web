<?php

declare(strict_types=1);

namespace App\Routing;

final class UrlVariant
{
    const CONSTANT = 0;

    const PLACEHOLDER = 1;

    private array $expected;

    private array $parts;

    public static function empty(): self
    {
        return new self;
    }

    private function __construct(array $expected = [], array $parts = [])
    {
        $this->expected = $expected;
        $this->parts = $parts;
    }

    public function withConstant(string $part): self
    {
        return new self($this->expected, [...$this->parts, [
            'type' => self::CONSTANT,
            'value' => $part,
        ]]);
    }

    public function withPlaceholder(string $key, string $regex = '/.+?/'): self
    {
        return new self(array_unique([...$this->expected, $key]), [...$this->parts, [
            'type' => self::PLACEHOLDER,
            'key' => $key,
            'regex' => $regex,
        ]]);
    }

    public function path(array $placeholders): MatchingResult
    {
        $matching = array_intersect(array_keys($placeholders), $this->expected);

        if (count($matching) == count($this->expected)) {
            return $this->result($this->parts, $placeholders);
        }

        return MatchingResult::failure();
    }

    private function result(array $parts, array $placeholders): MatchingResult
    {
        if (count($parts) == 0) {
            return MatchingResult::success('');
        }

        $head = array_shift($parts);

        if ($head['type'] == self::CONSTANT) {
            return MatchingResult::success($head['value'])->with($this->result($parts, $placeholders));
        }

        ['key' => $key, 'regex' => $regex] = $head;

        $placeholder = (string) $placeholders[$key];

        if (preg_match('~^' . $regex . '$~', $placeholder) !== 0) {
            return MatchingResult::success($placeholder)->with($this->result($parts, $placeholders));
        }

        return MatchingResult::placeholderFormatError($key, $regex, $placeholder);
    }
}
