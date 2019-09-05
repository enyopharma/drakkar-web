<?php

declare(strict_types=1);

namespace Infrastructure;

final class Efetch
{
    const REMOTE_URL = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi';

    public function metadata(int $pmid): array
    {
        // download xml using ncbi efetch.
        $url = self::REMOTE_URL . '?' . http_build_query([
            'db' => 'pubmed',
            'id' => $pmid,
            'retmode' => 'xml',
        ]);

        $contents = @file_get_contents($url);

        if ($contents === false) {
            return [
                'success' => false,
                'error' => error_get_last(),
            ];
        }

        // PUT CDATA AROUND TITLE AND ABSTRACT TO PARSE HTML
        $contents = (string) preg_replace('/<ArticleTitle(.*?)>(.+?)<\/ArticleTitle>/s', '<ArticleTitle$1><![CDATA[$2]]></ArticleTitle>', $contents);
        $contents = (string) preg_replace('/<AbstractText(.*?)>(.+?)<\/AbstractText>/s', '<AbstractText$1><![CDATA[$2]]></AbstractText>', $contents);

        // parse xml.
        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xml === false) {
            return [
                'success' => false,
                'errors' => libxml_get_errors(),
            ];
        }

        // convert xml to json.
        $json = json_encode($xml);

        if ($json === false) {
            return [
                'success' => false,
                'error' => json_last_error(),
            ];
        }

        // return the json data.
        return [
            'success' => true,
            'data' => $json,
        ];
    }
}
