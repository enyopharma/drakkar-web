<?php declare(strict_types=1);

namespace Shared\Http\Extensions\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

use Zend\Expressive\Helper\UrlHelper;

final class UrlExtension implements ExtensionInterface
{
    private $helper;

    public function __construct(UrlHelper $helper)
    {
        $this->helper = $helper;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('url', $this->helper);
    }
}
