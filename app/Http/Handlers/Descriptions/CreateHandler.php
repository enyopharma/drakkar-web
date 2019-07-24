<?php declare(strict_types=1);

namespace App\Http\Handlers\Descriptions;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\ReadModel\DrakkarInterface;
use App\ReadModel\NotFoundException;
use App\Http\Responders\HtmlResponder;

final class CreateHandler implements RequestHandlerInterface
{
    private $drakkar;

    private $responder;

    public function __construct(DrakkarInterface $drakkar, HtmlResponder $responder)
    {
        $this->drakkar = $drakkar;
        $this->responder = $responder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $attributes = (array) $request->getAttributes();

        $run_id = (int) $attributes['run_id'];
        $pmid = (int) $attributes['pmid'];

        try {
            $run = $this->drakkar->run($run_id);
            $publication = $run->publication($pmid);

            return $this->responder->template('descriptions/create', [
                'run' => $run->data(),
                'publication' => $publication->data(),
                'description' => [],
            ]);
        }

        catch (NotFoundException $e) {
            return $this->responder->notfound();
        }
    }
}
