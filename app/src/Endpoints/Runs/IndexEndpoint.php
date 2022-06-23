<?php

declare(strict_types=1);

namespace App\Endpoints\Runs;

use League\Plates\Engine;

use App\ReadModel\RunViewInterface;

#[\App\Attributes\Pattern('/')]
#[\App\Attributes\Name('runs.index')]
final class IndexEndpoint
{
    public function __construct(
        private Engine $engine,
        private RunViewInterface $runs,
    ) {
    }

    public function __invoke(): string
    {
        return $this->engine->render('runs/index', [
            'runs' => $this->runs->all('nbs')->fetchAll(),
        ]);
    }
}
