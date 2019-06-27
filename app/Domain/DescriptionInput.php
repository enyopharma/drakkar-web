<?php declare(strict_types=1);

namespace App\Domain;

use Psr\Http\Message\ServerRequestInterface;

final class DescriptionInput
{
    private $run_id;
    private $pmid;
    private $method;
    private $interactor1;
    private $interactor2;

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $attributes = (array) $request->getAttributes();
        $body = (array) $request->getParsedBody();

        return new self(
            (int) $attributes['run_id'],
            (int) $attributes['pmid'],
            $body['method'] ?? [],
            $body['interactor1'] ?? [],
            $body['interactor2'] ?? []
        );
    }

    public function __construct(int $run_id, int $pmid, array $method, array $interactor1, array $interactor2)
    {
        $this->run_id = $run_id;
        $this->pmid = $pmid;
        $this->method = $method;
        $this->interactor1 = $interactor1;
        $this->interactor2 = $interactor2;
    }

    public function run(): array
    {
        return ['id' => $this->run_id];
    }

    public function publication(): array
    {
        return ['pmid' => $this->pmid];
    }

    public function method(): array
    {
        if (! is_array($this->method)) {
            $this->error('Key method must be an array');
        }

        if (! key_exists('psimi_id', $this->method)) {
            $this->error('Key method.psimi_id is required');
        }

        return [
            'psimi_id' => $this->psimi_id($this->method['psimi_id']),
        ];
    }

    public function interactor1(): array
    {
        return $this->interactor(1, $this->interactor1);
    }

    public function interactor2(): array
    {
        return $this->interactor(2, $this->interactor2);
    }

    private function psimi_id($psimi_id): string
    {
        if (! is_string($psimi_id)) {
            $this->error('Key method.psimi_id must be a string');
        }

        if (preg_match('/^MI:[0-9]+$/', $psimi_id) === 0) {
            $this->error('Key method.psimi_id must be formatted as MI:[0-9]+');
        }

        return $psimi_id;
    }

    private function interactor(int $i, $interactor): array
    {
        if (! is_array($interactor)) {
            $this->error('Key interactor%s must be an array', $i);
        }

        if (! key_exists('name', $interactor)) {
            $this->error('Key interactor%s.name is required', $i);
        }

        if (! key_exists('start', $interactor)) {
            $this->error('Key interactor%s.start is required', $i);
        }

        if (! key_exists('stop', $interactor)) {
            $this->error('Key interactor%s.stop is required', $i);
        }

        if (! key_exists('protein', $interactor)) {
            $this->error('Key interactor%s.protein is required', $i);
        }

        if (! key_exists('mapping', $interactor)) {
            $this->error('Key interactor%s.mapping is required', $i);
        }

        $name = $this->name($i, $interactor['name']);
        $start = $this->start($i, $interactor['start']);
        $stop = $this->stop($i, $interactor['stop']);

        if ($start > $stop) {
            $this->error('Key interactor%s: start is greater stop', $i);
        }

        $protein = $this->protein($i, $interactor['protein']);
        $mapping = $this->mapping($i, $interactor['mapping']);

        return [
            'name' => $name,
            'start' => $start,
            'stop' => $stop,
            'protein' => $protein,
            'mapping' => $mapping,
        ];
    }

    private function name(int $i, $name): string
    {
        if (! is_string($name)) {
            $this->error('Key interactor%s.name must be a string', $i);
        }

        if (preg_match('/^[a-zA-Z0-9]+$/', $name) === 0) {
            $this->error('Key interactor%s.name must be formatted as [a-zA-Z0-9]+', $i);
        }

        return $name;
    }

    private function start(int $i, $start): int
    {
        if (! is_int($start)) {
            $this->error('Key interactor%s.start must be an integer', $i);
        }

        if ($start < 1) {
            $this->error('Key interactor%s.start must be greater than 0', $i);
        }

        return $start;
    }

    private function stop(int $i, $stop): int
    {
        if (! is_int($stop)) {
            $this->error('Key interactor%s.stop must be an integer', $i);
        }

        if ($stop < 1) {
            $this->error('Key interactor%s.stop must be greater than 0', $i);
        }

        return $stop;
    }

    private function protein(int $i, $protein): array
    {
        if (! is_array($protein)) {
            $this->error('Key interactor%s.protein must be an array', $i);
        }

        if (! key_exists('accession', $protein)) {
            $this->error('Key interactor%s.protein.accession is required', $i);
        }

        return [
            'accession' => $this->protacc($i, $protein['accession']),
        ];
    }

    private function protacc(int $i, $accession): string
    {
        if (! is_string($accession)) {
            $this->error('Key interactor%s.protein.accession must be a string', $i);
        }

        if (preg_match('/^[A-Z0-9]+$/', $accession) === 0) {
            $this->error('Key interactor%s.protein.accession must be formatted as [A-Z0-9]+', $i);
        }

        return $accession;
    }

    private function mapping(int $i, $mapping): array
    {
        if (! is_array($mapping)) {
            $this->error('Key interactor%s.mapping must be an array', $i);
        }

        if (count($mapping) > count(array_filter(array_keys($mapping), 'is_int'))) {
            $this->error('Key interactor%s.mapping must be a non associative array', $i);
        }

        return array_map(function ($j, $alignment) use ($i) {
            return $this->alignment($i, $j, $alignment);
        }, array_keys($mapping), array_values($mapping));
    }

    private function alignment(int $i, int $j, $alignment): array
    {
        if (! is_array($alignment)) {
            $this->error('Key interactor%s.mapping[%s] must be an array', $i, $j);
        }

        if (! key_exists('sequence', $alignment)) {
            $this->error('Key interactor%s.mapping[%s].sequence is required', $i, $j);
        }

        if (! key_exists('isoforms', $alignment)) {
            $this->error('Key interactor%s.mapping[%s].isoforms is required', $i, $j);
        }

        return [
            'sequence' => $this->sequence($i, $j, $alignment['sequence']),
            'isoforms' => $this->isoforms($i, $j, $alignment['isoforms']),
        ];
    }

    private function sequence(int $i, int $j, $sequence): string
    {
        if (! is_string($sequence)) {
            $this->error('Key interactor%s.mapping[%s].sequence must be a string', $i, $j);
        }

        if (preg_match('/^[ARNDCQEGHILKMFPSTWYV]+$/', $sequence) === 0) {
            $this->error('Key interactor%s.mapping[%s].sequence must be formatted as [ARNDCQEGHILKMFPSTWYV]+', $i, $j);
        }

        return $sequence;
    }

    private function isoforms(int $i, int $j, $isoforms): array
    {
        if (! is_array($isoforms)) {
            $this->error('Key interactor%s.mapping[%s].isoforms must be an array', $i, $j);
        }

        if (count($isoforms) == 0) {
            $this->error('Key interactor%s.mapping[%s].isoforms must not be empty', $i, $j);
        }

        if (count($isoforms) > count(array_filter(array_keys($isoforms), 'is_int'))) {
            $this->error('Key interactor%s.mapping[%s].isoforms must be a non associative array', $i, $j);
        }

        return array_map(function ($k, $isoform) use ($i, $j) {
            return $this->isoform($i, $j, $k, $isoform);
        }, array_keys($isoforms), array_values($isoforms));
    }

    private function isoform(int $i, int $j, int $k, $isoform): array
    {
        if (! is_array($isoform)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s] must be an array', $i, $j, $k);
        }

        if (! key_exists('accession', $isoform)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].accession is required', $i, $j, $k);
        }

        if (! key_exists('occurrences', $isoform)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences is required', $i, $j, $k);
        }

        return [
            'accession' => $this->isoacc($i, $j, $k, $isoform['accession']),
            'occurrences' => $this->occurrences($i, $j, $k, $isoform['occurrences']),
        ];
    }

    private function isoacc(int $i, int $j, int $k, $accession): string
    {
        if (! is_string($accession)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].accession must be a string', $i, $j, $k);
        }

        if (preg_match('/^[-A-Z0-9]+$/', $accession) === 0) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].accession must be formatted as [-A-Z0-9]+', $i, $j, $k);
        }

        return $accession;
    }

    private function occurrences(int $i, int $j, int $k, $occurrences): array
    {
        if (! is_array($occurrences)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences must be an array', $i, $j, $k);
        }

        if (count($occurrences) == 0) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences must not be empty', $i, $j, $k);
        }

        if (count($occurrences) > count(array_filter(array_keys($occurrences), 'is_int'))) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences must be a non associative array', $i, $j, $k);
        }

        return array_map(function ($l, $occurrence) use ($i, $j, $k) {
            return $this->occurrence($i, $j, $k, $l, $occurrence);
        }, array_keys($occurrences), array_values($occurrences));
    }

    private function occurrence(int $i, int $j, int $k, int $l, $occurrence): array
    {
        if (! is_array($occurrence)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s] must be an array', $i, $j, $k, $l);
        }

        if (! key_exists('start', $occurrence)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].start is required', $i, $j, $k, $l);
        }

        if (! key_exists('stop', $occurrence)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].stop is required', $i, $j, $k, $l);
        }

        if (! key_exists('identity', $occurrence)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].identity is required', $i, $j, $k, $l);
        }

        $start = $this->occstart($i, $j, $k, $l, $occurrence['start']);
        $stop = $this->occstop($i, $j, $k, $l, $occurrence['stop']);

        if ($start > $stop) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s]: start is greater than stop', $i, $j, $k, $l);
        }

        $identity = $this->identity($i, $j, $k, $l, $occurrence['identity']);

        return [
            'start' => $start,
            'stop' => $stop,
            'identity' => $identity,
        ];
    }

    private function occstart(int $i, int $j, int $k, int $l, $start): int
    {
        if (! is_int($start)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].start must be an integer', $i, $j, $k, $l);
        }

        if ($start < 1) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].start must be greater than 0', $i, $j, $k, $l);
        }

        return $start;
    }

    private function occstop(int $i, int $j, int $k, int $l, $stop): int
    {
        if (! is_int($stop)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].stop must be an integer', $i, $j, $k, $l);
        }

        if ($stop < 1) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].stop must be greater than 0', $i, $j, $k, $l);
        }

        return $stop;
    }

    private function identity(int $i, int $j, int $k, int $l, $identity)
    {
        if (! is_int($identity) && ! is_float($identity)) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].identity must be a number', $i, $j, $k, $l);
        }

        if ($identity < 1) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].identity must be greater than 0', $i, $j, $k, $l);
        }

        if ($identity > 100) {
            $this->error('Key interactor%s.mapping[%s].isoforms[%s].occurrences[%s].identity must be smaller than 100', $i, $j, $k, $l);
        }

        return $identity;
    }

    private function error(string $format, ...$xs)
    {
        throw new \UnexpectedValueException(vsprintf($format, $xs));
    }
}
