<?php

namespace Enyo\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;

class SsoAuthentificationMiddleware implements MiddlewareInterface
{
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookies = $request->getCookieParams();

        $session_id = $cookies['sso'] ?? '';

        $client = new \GuzzleHttp\Client(['base_uri' => 'http://' . getenv('SSO_HOST')]);

        $response = $client->request('GET', '/', ['allow_redirects' => false, 'headers' => [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $session_id,
        ]]);

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody()->getContents(), true);

            $source = $data['source'];
            $user = $data['user'];
            $options = $data['options'];

            $request = $request->withAttribute('source', $source);
            $request = $request->withAttribute('user', $user);

            $response = $handler->handle($request);

            $cookie = $this->createCookie($session_id, $options);

            return FigResponseCookies::set($response, $cookie);
        }

        if ($response->getStatusCode() == 302) {
            $location = $response->getHeaderLine('Location');

            $url = $location . '?' . http_build_query([
                'redirect' => urlencode($request->getUri()),
            ]);

            return $this->factory
                ->createResponse(302)
                ->withHeader('location', $url);
        }

        throw new \Exception('Sso error');
    }

    private function createCookie(string $session_id, array $options): SetCookie
    {
        $cookie_name = $options['name'];
        $cookie_lifetime = $options['lifetime'] < 0 ? 0 : $options['lifetime'];
        $cookie_path = $options['path'];
        $cookie_domain = $options['domain'];
        $secure = $options['secure'];
        $httponly = $options['httponly'];

        $cookie = SetCookie::create($cookie_name, $session_id)
            ->withMaxAge($cookie_lifetime)
            ->withPath($cookie_path)
            ->withDomain($cookie_domain)
            ->withSecure($secure)
            ->withHttpOnly($httponly);

        if ($cookie_lifetime > 0) {

            $cookie = $cookie->withExpires(time() + $cookie_lifetime);

        }

        return $cookie;
    }
}
