<?php

declare(strict_types=1);

namespace Domain;

final class Description implements ResourceInterface
{
    private $run_id;

    private $pmid;

    private $id;

    public function __construct(int $run_id, int $pmid, int $id)
    {
        $this->run_id = $run_id;
        $this->pmid = $pmid;
        $this->id = $id;
    }

    public function id(): array
    {
        return [
            'run_id' => $this->run_id,
            'pmid' => $this->pmid,
            'id' => $this->id,
        ];
    }
}
