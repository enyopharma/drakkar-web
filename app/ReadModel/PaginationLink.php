<?php declare(strict_types=1);

namespace App\ReadModel;

final class PaginationLink
{
    private $page;

    private $current;

    private $disabled;

    public function __construct(int $page, int $current, bool $disabled)
    {
        $this->page = $page;
        $this->current = $current;
        $this->disabled = $disabled;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function isActive(): bool
    {
        return $this->page == $this->current;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}
