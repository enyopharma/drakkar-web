<?php

declare(strict_types=1);

namespace Domain\Actions;

interface DeleteDescriptionInterface
{
    public function delete(int $id): DeleteDescriptionResult;
}
