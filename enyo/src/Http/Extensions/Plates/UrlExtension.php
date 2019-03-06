<?php declare(strict_types=1);

namespace Enyo\Http\Extensions\Plates;

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
        $engine->registerFunction('partialUrl', [$this, 'partial']);
    }

    public function partial(...$xs): callable
    {
        return function (array $query = [], string $fragment = null, array $options = []) use ($xs) {
            return ($this->helper)(
                $xs[0] ?? null,
                $xs[1] ?? [],
                ($xs[2] ?? []) + $query,
                is_null($fragment) ? ($xs[3] ?? null) : $fragment,
                ($xs[4] ?? []) + $options
            );
        };
    }
}
