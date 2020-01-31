<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Quanta\Validation\ErrorInterface;

use Domain\Input\DescriptionInput;
use Domain\Validations\Association;

use App\Http\Responders\JsonResponder;

final class ValidateDescriptionMiddleware implements MiddlewareInterface
{
    private $responder;

    private $pdo;

    const SELECT_ASSOCIATION_SQL = <<<SQL
        SELECT a.id, r.type
        FROM runs AS r, associations AS a
        WHERE r.id = a.run_id
        AND a.run_id = ?
        AND a.pmid = ?
SQL;

    public function __construct(JsonResponder $responder, \PDO $pdo)
    {
        $this->responder = $responder;
        $this->pdo = $pdo;
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

        if (! $association = $select_association_sth->fetch()) {
            return $this->responder->notFound();
        }

        $association = new Association($association['id'], $association['type']);

        // validate the input.
        return DescriptionInput::from($this->pdo, $association, $data)->extract(
            function (DescriptionInput $input) use ($request, $handler) {
                return $this->success($request, $handler, $input);
            },
            function (ErrorInterface ...$errors) {
                return $this->failure(...$errors);
            }
        );
    }

    private function success(ServerRequestInterface $request, RequestHandlerInterface $handler, DescriptionInput $input): ResponseInterface
    {
        $request = $request->withAttribute(DescriptionInput::class, $input);

        return $handler->handle($request);
    }

    private function failure(ErrorInterface ...$errors): ResponseInterface
    {
        return $this->responder->errors(...array_map([$this, 'message'], $errors));
    }

    private function message(ErrorInterface $error): string
    {
        $name = $error->name();

        return $name == ''
            ? $error->message()
            : $name . ' => ' . $error->message();
    }
}
