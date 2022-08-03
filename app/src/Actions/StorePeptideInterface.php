<?php

declare(strict_types=1);

namespace App\Actions;

use App\Input\Peptide;

interface StorePeptideInterface
{
    public function store(int $run_id, int $pmid, int $description_id, Peptide $peptide): StorePeptideResult;
}
