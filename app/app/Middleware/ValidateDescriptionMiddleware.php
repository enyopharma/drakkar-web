<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Quanta\Validation\Error;
use Quanta\Validation\InvalidDataException;

use App\Input\DataSource;
use App\Input\Association;
use App\Input\DescriptionInput;

final class ValidateDescriptionMiddleware implements MiddlewareInterface
{
    private \PDO $pdo;

    private ResponseFactoryInterface $factory;

    const SELECT_ASSOCIATION_SQL = <<<SQL
        SELECT a.id, r.type
        FROM runs AS r, associations AS a
        WHERE r.id = a.run_id
        AND a.run_id = ?
        AND a.pmid = ?
    SQL;

    public function __construct(\PDO $pdo, ResponseFactoryInterface $factory)
    {
        $this->pdo = $pdo;
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // get the input.
        $run_id = (int) $request->getAttribute('run_id');
        $pmid = (int) $request->getAttribute('pmid');

        // get the description data.
        $data = (array) $request->getParsedBody();

        // get the association.
        $select_association_sth = $this->pdo->prepare(self::SELECT_ASSOCIATION_SQL);

        $select_association_sth->execute([$run_id, $pmid]);

        if (!$association = $select_association_sth->fetch()) {
            return $this->factory->createResponse(404);
        }

        $association = new Association($association['id'], $association['type']);

        // get the factory.
        $factory = DescriptionInput::factory(new DataSource($this->pdo), $association);

        // try to produce a description input.
        try {
            $input = $factory($data);

            $request = $request->withAttribute(DescriptionInput::class, $input);

            return $handler->handle($request);
        }

        catch (InvalidDataException $e) {
            return $this->failure(...$e->errors());
        }
    }

    private function failure(Error ...$errors): ResponseInterface
    {
        $contents = json_encode([
            'code' => 422,
            'success' => false,
            'errors' => array_map([$this, 'message'], $errors),
            'data' => [],
        ], JSON_THROW_ON_ERROR);

        $response = $this->factory
            ->createResponse(422)
            ->withHeader('content-type', 'application/json');

        $response->getBody()->write($contents);

        return $response;
    }

    private function message(Error $error): string
    {
        $name = array_map(fn ($key) => '[' . $key . ']', $error->keys());
        $name = implode('', $name);

        return $name == ''
            ? $error->message()
            : $name . ' => ' . $error->message();
    }
}
