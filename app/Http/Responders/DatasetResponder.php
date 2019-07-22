<?php declare(strict_types=1);

namespace App\Http\Responders;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use App\ReadModel\ResultSetInterface;

final class DatasetResponder
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function response(ResultSetInterface $rset, string $filename = 'vinland'): ResponseInterface
    {
        return $this->factory->createResponse(200)
            ->withBody(new IterableJsonStream($rset))
            ->withHeader('content-type', 'application/json')
            ->withHeader('content-disposition', sprintf('attachment; filename="%s.json"', $filename));
    }
}
