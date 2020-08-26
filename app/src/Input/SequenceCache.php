<?php

declare(strict_types=1);

namespace App\Input;

final class SequenceCache
{
    private string $accession;

    private int $start;

    private int $stop;

    private array $sequences;

    public function __construct(string $accession, int $start, int $stop, array $sequences)
    {
        $this->accession = $accession;
        $this->start = $start;
        $this->stop = $stop;
        $this->sequences = $sequences;
    }

    /**
     * @return string|false
     */
    public function sequence(string $accession)
    {
        if (array_key_exists($accession, $this->sequences)) {
            return $accession == $this->accession
                ? substr($this->sequences[$accession], $this->start - 1, $this->stop - $this->start + 1)
                : $this->sequences[$accession];
        }

        return false;
    }
}
