<?php

declare(strict_types=1);

namespace App\Http\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use App\Http\UrlGenerator;

final class UrlExtension implements ExtensionInterface
{
    private $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('url', [$this->url, 'generate']);
    }
}
