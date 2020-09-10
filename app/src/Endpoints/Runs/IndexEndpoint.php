<?php

declare(strict_types=1);

namespace App\Endpoints\Runs;

use League\Plates\Engine;

use App\ReadModel\RunViewInterface;

final class IndexEndpoint
{
    private Engine $engine;

    private RunViewInterface $runs;

    public function __construct(Engine $engine, RunViewInterface $runs)
    {
        $this->engine = $engine;
        $this->runs = $runs;
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        return $this->engine->render('runs/index', [
            'runs' => $this->runs->all('nbs')->fetchAll(),
        ]);
    }
}
