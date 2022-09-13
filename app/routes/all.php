<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Quanta\Http\Route;
use Quanta\Http\Endpoint;
use Quanta\Http\MethodList;
use Quanta\Http\MetadataSerializer;

$classes = [
    App\Endpoints\Runs\IndexEndpoint::class,
    App\Endpoints\Publications\SearchEndpoint::class,
    App\Endpoints\Publications\IndexEndpoint::class,
    App\Endpoints\Publications\UpdateEndpoint::class,
    App\Endpoints\Descriptions\SearchEndpoint::class,
    App\Endpoints\Descriptions\IndexEndpoint::class,
    App\Endpoints\Descriptions\CreateEndpoint::class,
    App\Endpoints\Descriptions\EditEndpoint::class,
    App\Endpoints\Descriptions\StoreEndpoint::class,
    App\Endpoints\Descriptions\DeleteEndpoint::class,
    App\Endpoints\Peptides\IndexEndpoint::class,
    App\Endpoints\Peptides\StoreEndpoint::class,
    App\Endpoints\Methods\IndexEndpoint::class,
    App\Endpoints\Methods\ShowEndpoint::class,
    App\Endpoints\Proteins\IndexEndpoint::class,
    App\Endpoints\Proteins\ShowEndpoint::class,
    App\Endpoints\Taxa\ShowEndpoint::class,
    App\Endpoints\Alignments\StartEndpoint::class,
    App\Endpoints\Dataset\DownloadEndpoint::class,
];

/**
 * Return route definitions from attributes.
 *
 * @param Psr\Container\ContainerInterface $container
 * @return array<int, Quanta\Http\Route>
 */
return function (ContainerInterface $container) use ($classes): array {
    $factory = $container->get(ResponseFactoryInterface::class);

    $serializer = new MetadataSerializer('data', ['success' => true, 'code' => 200]);

    $endpoint = fn (callable $f) => new Endpoint($factory, $f, $serializer);

    $routes = [];

    foreach ($classes as $class) {
        $parsed = ParsedEndpoint::from($class);

        if (count($parsed->patterns) == 0) {
            throw new LogicException('pattern is required');
        }

        if (count($parsed->patterns) > 1) {
            throw new LogicException('only one pattern allowed');
        }

        if (count($parsed->names) > 1) {
            throw new LogicException('only one name allowed');
        }

        $methods = count($parsed->methods) > 0
            ? new MethodList(...$parsed->methods[0]->values)
            : MethodList::get();

        $route = Route::matching($parsed->patterns[0]->value);

        if (count($parsed->names) == 1) {
            $route = $route->named($parsed->names[0]->value);
        }

        foreach ($parsed->middlewares as $middleware) {
            $route = $route->middleware(fn () => $container->get($middleware->value));
        }

        $routes[] = $route->route($methods, fn () => $endpoint($container->get($class)));
    }

    return $routes;
};

final class ParsedEndpoint
{
    public static function from(string $class): self
    {
        $methods = [];
        $patterns = [];
        $names = [];
        $middlewares = [];

        $reflection = new ReflectionClass($class);

        $attr['methods'] = $reflection->getAttributes(App\Attributes\Method::class);
        $attr['patterns'] = $reflection->getAttributes(App\Attributes\Pattern::class);
        $attr['names'] = $reflection->getAttributes(App\Attributes\Name::class);
        $attr['middlewares'] = $reflection->getAttributes(App\Attributes\Middleware::class);

        foreach ($attr['methods'] as $method) $methods[] = $method->newInstance();
        foreach ($attr['patterns'] as $pattern) $patterns[] = $pattern->newInstance();
        foreach ($attr['names'] as $name) $names[] = $name->newInstance();
        foreach ($attr['middlewares'] as $middleware) $middlewares[] = $middleware->newInstance();

        return new self($methods, $patterns, $names, $middlewares);
    }

    private function __construct(
        public readonly array $methods,
        public readonly array $patterns,
        public readonly array $names,
        public readonly array $middlewares,
    ) {
    }
}
