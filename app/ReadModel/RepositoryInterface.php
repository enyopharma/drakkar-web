<?php declare(strict_types=1);

namespace App\ReadModel;

interface RepositoryInterface
{
    public function projection(string $name, ...$xs): ProjectionInterface;
}
