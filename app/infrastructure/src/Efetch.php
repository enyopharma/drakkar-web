<?php

declare(strict_types=1);

namespace Infrastructure;

final class Efetch
{
    const REMOTE_URL = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi';

    public function metadata(int $pmid): string
    {
        // download xml using ncbi efetch.
        $url = self::REMOTE_URL . '?' . http_build_query(['db' => 'pubmed', 'id' => $pmid, 'retmode' => 'xml']);

        $xml = @file_get_contents($url);

        if ($xml === false) {
            $error = error_get_last() ?? ['message' => ''];

            throw new InfrastructureException($error['message']);
        }

        // PUT CDATA AROUND TITLE AND ABSTRACT TO PARSE HTML
        $xml = (string) preg_replace('/<ArticleTitle(.*?)>(.+?)<\/ArticleTitle>/s', '<ArticleTitle$1><![CDATA[$2]]></ArticleTitle>', $xml);
        $xml = (string) preg_replace('/<AbstractText(.*?)>(.+?)<\/AbstractText>/s', '<AbstractText$1><![CDATA[$2]]></AbstractText>', $xml);

        // parse xml.
        libxml_use_internal_errors(true);

        $metadata = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($metadata === false) {
            throw new InfrastructureException(implode("\n", libxml_get_errors()));
        }

        // convert xml to json.
        $json = json_encode($metadata);

        if ($json === false) {
            throw new InfrastructureException(json_last_error_msg());
        }

        // return the json data.
        return $json;
    }
}
