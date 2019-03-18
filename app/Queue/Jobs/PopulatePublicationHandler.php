<?php declare(strict_types=1);

namespace App\Queue\Jobs;

use App\Domain\PopulatePublication;

use Enyo\Queue\JobSuccess;
use Enyo\Queue\JobFailure;
use Enyo\Queue\JobResultInterface;
use Enyo\Queue\JobHandlerInterface;

final class PopulatePublicationHandler implements JobHandlerInterface
{
    private $domain;

    public function __construct(PopulatePublication $domain)
    {
        $this->domain = $domain;
    }

    public function __invoke(array $input): JobResultInterface
    {
        $pmid = (int) $input['pmid'];

        return ($this->domain)($pmid)->parsed($this->bind('success'), [
            PopulatePublication::QUERY_FAILED => $this->bind('queryFailed', $pmid),
            PopulatePublication::PARSING_FAILED => $this->bind('parsingFailed', $pmid),
            PopulatePublication::NOT_FOUND => $this->bind('notFound', $pmid),
        ]);
    }

    private function bind(string $method, ...$xs)
    {
        return function ($data) use ($method, $xs) {
            return $this->{$method}(...array_merge($xs, [$data]));
        };
    }

    private function success(): JobResultInterface
    {
        return new JobSuccess;
    }

    private function queryFailed(int $pmid): JobResultInterface
    {
        return new JobFailure(sprintf('Query failed for publication %s', $pmid));
    }

    private function parsingFailed(int $pmid, array $data): JobResultInterface
    {
        return new JobFailure(implode("\n", [
            vsprintf('Metadata parsing failed with code %s for pmid %s', [
                $data['error'],
                $pmid,
            ]),
            'Retrieved contents:',
            $data['contents'],
        ]));
    }

    private function notFound(int $pmid): JobResultInterface
    {
        return new JobFailure(sprintf('No publication with pmid %s', $pmid));
    }
}
