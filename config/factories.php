<?php declare(strict_types=1);

use Quanta\Container\Maps\FactoryMapInterface;

/**
 * Return an associative array of factories from the given factory map.
 *
 * @param \Quanta\Container\Maps\FactoryMapInterface $map
 * @return callable[]
 */
return function (FactoryMapInterface $map): array {
    return $map->factories();
};
