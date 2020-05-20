<?php

declare(strict_types=1);

namespace App\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use App\Helpers\UrlGenerator;

final class UrlExtension implements ExtensionInterface
{
    private UrlGenerator $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function register(Engine $engine): void
    {
        $engine->registerFunction('url', [$this->url, 'generate']);
    }
}
