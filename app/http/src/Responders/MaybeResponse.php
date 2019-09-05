<?php

declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;

final class MaybeResponse
{
    const JUST = 0;
    const NONE = 1;

    private $type;

    private $response;

    public static function just(ResponseInterface $response): self
    {
        return new self(self::JUST, $response);
    }

    public static function none(): self
    {
        return new self(self::NONE);
    }

    private function __construct(int $type, ResponseInterface $response = null)
    {
        $this->type = $type;
        $this->response = $response;
    }

    public function isJust(): bool
    {
        return self::JUST == $this->type;
    }

    public function isNone(): bool
    {
        return self::NONE == $this->type;
    }

    public function response(): ResponseInterface
    {
        if (self::JUST == $this->type) {
            return $this->response;
        }

        throw new \LogicException('No response available');
    }
}
