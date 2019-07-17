<?php declare(strict_types=1);

namespace App\ReadModel;

interface ProjectionInterface
{
    public function rset(array $criteria = []): ResultSetInterface;
}
