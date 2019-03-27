<?php declare(strict_types=1);

namespace App\Cli\Responders;

use Symfony\Component\Console\Output\OutputInterface;

final class PopulateResponder
{
    private $responder;

    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
    }

    public function default(string $tpl, OutputInterface $output, ...$xs): void
    {
        $this->responder->default($tpl, $output, ...$xs);
    }

    public function info(string $tpl, OutputInterface $output, ...$xs): void
    {
        $this->responder->info($tpl, $output, ...$xs);
    }

    public function error(string $tpl, OutputInterface $output, ...$xs): void
    {
        $this->responder->error($tpl, $output, ...$xs);
    }

    public function success(OutputInterface $output, int $pmid): void
    {
        $this->responder->info('Metadata of publication with pmid %s successfully updated.', ...[
            $output,
            $pmid,
        ]);
    }

    public function efetchError(OutputInterface $output, int $pmid, array $data): void
    {
        if ($data['type'] == Efetch::DOWNLOAD_ERROR) {
            $this->downloadFailed($output, $pmid, $data);
        }

        if ($data['type'] == Efetch::PARSING_ERROR) {
            $this->parsingError($output, $pmid, $data);
        }

        if ($data['type'] == Efetch::CONVERSION_ERROR) {
            $this->conversionError($output, $pmid, $data);
        }

        $this->responder->error('Unknown efetch error for publication with pmid %s', ...[
            $output,
            $pmid,
        ]);
    }

    private function downloadFailed(OutputInterface $output, int $pmid, array $data)
    {
        $this->responder->error('Failed to download the metadata of publication with pmid %s', ...[
            $output,
            $pmid,
        ]);

        $this->responder->default($data['url']);
        $this->responder->default($data['error']);
    }

    private function parsingFailed(OutputInterface $output, int $pmid, array $data)
    {
        $this->responder->error('Failed to parse the metadata of publication with pmid %s', ...[
            $output,
            $pmid,
        ]);

        $this->responder->default($data['xml']);

        foreach ($data['errors'] as $error) {
            $this->responder->default($error);
        }
    }

    private function conversionFailed(OutputInterface $output, int $pmid, array $data)
    {
        $this->responder->error('Failed to convert the metadata of publication with pmid %s to json', ...[
            $output,
            $pmid,
        ]);

        $this->responder->default($data['xml']);

        foreach ($data['errors'] as $error) {
            $this->responder->default($error);
        }
    }
}
