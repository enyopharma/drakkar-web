<?php declare(strict_types=1);

namespace Utils\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class HttpErrorMiddleware implements MiddlewareInterface
{
    private $factory;

    public function __construct(StreamFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $status = $response->getStatusCode();

        if ($status == 200) {
            return $response;
        }

        $accept = $request->getHeaderLine('accept');
        $reason = $response->getReasonPhrase();

        $body = $this->stream($status, $reason, $accept);

        return $response->withBody($body);
    }

    private function stream(int $status, string $reason, string $accept): StreamInterface
    {
        if (stripos($accept, 'text/html') !== false) {
            return $this->html($status, $reason);
        }

        if (stripos($accept, 'application/json') !== false) {
            return $this->json($status, $reason);
        }

        return $this->plain($status, $reason);
    }

    private function html(int $status, string $reason): StreamInterface
    {
        $tpl = <<<EOT
<!DOCTYPE html>
<html>
    <head>
        <title>%s</title>
    </head>
    <body>
        <h1>Response status: %s</h1>
        <p>
            %s
        </p>
    </body>
</html>
EOT;

        $xs[] = $status;

        if ($reason != '') $xs[] = $reason;

        return $this->factory->createStream(vsprintf($tpl, [
            implode(' ', $xs), $status, $reason == '' ? '-' : $reason,
        ]));
    }

    private function json(int $status, string $reason): StreamInterface
    {
        return $this->factory->createStream(json_encode([
            'status' => $status,
            'reason' => $reason,
        ]));
    }

    private function plain(int $status, string $reason): StreamInterface
    {
        $xs[] = $status;

        if ($reason != '') $xs[] = $reason;

        return $this->factory->createStream(implode(' ', $xs));
    }
}
