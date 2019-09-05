<?php

declare(strict_types=1);

namespace App\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

final class NyholmContext
{
    private $application;

    public function __construct(RequestHandlerInterface $application)
    {
        $this->application = $application;
    }

    public function __invoke(): ResponseInterface
    {
        $factory = new Psr17Factory;

        $creator = new ServerRequestCreator($factory, $factory, $factory, $factory);

        $request = $creator->fromGlobals();

        return $this->application->handle($request);
    }
}
