<?php declare(strict_types=1);

namespace Enyo\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionMiddleware implements MiddlewareInterface
{
    /**
     * Session options disabling automatic php session cookie management when
     * calling session_start().
     *
     * @var array
     */
    const SESSION_START_OPTIONS = [
        'use_trans_sid' => false,
        'use_cookies' => false,
        'use_only_cookies' => true,
        'cache_limiter' => '',
    ];

    /**
     * The date format for the session headers.
     *
     * @var string
     */
    const DATE_FORMAT = 'D, d-M-Y H:i:s T';

    /**
     * The date for the expired value of cache limiter header.
     *
     * @var string
     */
    const EXPIRED = 'Thu, 19 Nov 1981 08:52:00 GMT';

    /**
     * The mutable session.
     *
     * @var \Enyo\Http\Session
     */
    private $session;

    /**
     * Constructor.
     *
     * @param \Enyo\Http\Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Fail when session is disabled.
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new \RuntimeException(
                vsprintf('%s: Session is disabled', [
                    SessionMiddleware::class,
                ])
            );
        }

        // Fail when session is already started.
        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new \LogicException(
                vsprintf('%s: A session had already been started', [
                    SessionMiddleware::class,
                ])
            );
        }

        // Set the session id from the request.
        $name = session_name();

        $params = $request->getCookieParams();

        session_id($params[$name] ?? '');

        // Start the session with options disabling cookies.
        if (session_start(self::SESSION_START_OPTIONS)) {
            // Get the session as an object.
            $session = $this->session->populate($_SESSION);

            // Empty the session globals.
            $_SESSION = [];

            // Attach the session to the request.
            $request = $request->withAttribute(Session::class, $session);

            // Get a response from the request handler.
            $response = $handler->handle($request);

            // Populate the session globals with the mutated session data.
            $_SESSION = $this->session->data($request);

            // Write the session data and close the session.
            session_write_close();

            // Return a response with session headers attached.
            return $this->attachSessionHeaders($response);
        }

        throw new \RuntimeException(
            vsprintf('%s: session_start() returned false', [
                SessionMiddleware::class
            ])
        );
    }

    /**
     * Attach the session headers to the given response.
     *
     * Trying to emulate the default php 7.0 headers generations. Adapted from
     * Relay.Middleware SessionHeadersHandler.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @see https://github.com/relayphp/Relay.Middleware/blob/1.x/src/SessionHeadersHandler.php
     */
    private function attachSessionHeaders(ResponseInterface $response): ResponseInterface
    {
        $time = time();

        $response = $this->attachCacheLimiterHeader($response, $time);
        $response = $this->attachSessionCookie($response, $time);

        return $response;
    }

    /**
     * Attach a session cache limiter header to the given response.
     *
     * @param \Psr\Http\Message\ResponseInterface   $response
     * @param int                                   $time
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function attachCacheLimiterHeader(ResponseInterface $response, int $time): ResponseInterface
    {
        $cache_limiter = session_cache_limiter();

        switch ($cache_limiter) {
            case 'public':
                return $this->attachPublicCacheLimiterHeader($response, $time);
            case 'private':
                return $this->attachPrivateCacheLimiterHeader($response, $time);
            case 'private_no_expire':
                return $this->attachPrivateNoExpireCacheLimiterHeader($response, $time);
            case 'nocache':
                return $this->attachNocacheCacheLimiterHeader($response);
            default:
                return $response;
        }
    }

    /**
     * Attach a public cache limiter header to the given response.
     *
     * @param \Psr\Http\Message\ResponseInterface   $response
     * @param int                                   $time
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @see https://github.com/php/php-src/blob/PHP-7.0/ext/session/session.c#L1267-L1284
     */
    private function attachPublicCacheLimiterHeader(ResponseInterface $response, int $time): ResponseInterface
    {
        $cache_expire = session_cache_expire();

        $max_age = $cache_expire * 60;
        $expires = gmdate(self::DATE_FORMAT, $time + $max_age);
        $cache_control = "public, max-age={$max_age}";
        $last_modified = gmdate(self::DATE_FORMAT, $time);

        return $response
            ->withAddedHeader('Expires', $expires)
            ->withAddedHeader('Cache-Control', $cache_control)
            ->withAddedHeader('Last-Modified', $last_modified);
    }

    /**
     * Attach a private cache limiter header to the given response.
     *
     * @param \Psr\Http\Message\ResponseInterface   $response
     * @param int                                   $time
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @see https://github.com/php/php-src/blob/PHP-7.0/ext/session/session.c#L1297-L1302
     */
    private function attachPrivateCacheLimiterHeader(ResponseInterface $response, int $time): ResponseInterface
    {
        $response = $response->withAddedHeader('Expires', self::EXPIRED);

        return $this->attachPrivateNoExpireCacheLimiterHeader($response, $time);
    }

    /**
     * Attach a private_no_expire cache limiter header to the given response.
     *
     * @param \Psr\Http\Message\ResponseInterface   $response
     * @param int                                   $time
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @see https://github.com/php/php-src/blob/PHP-7.0/ext/session/session.c#L1286-L1295
     */
    private function attachPrivateNoExpireCacheLimiterHeader(ResponseInterface $response, int $time): ResponseInterface
    {
        $cache_expire = session_cache_expire();

        $max_age = $cache_expire * 60;
        $cache_control = "private, max-age={$max_age}";
        $last_modified = gmdate(self::DATE_FORMAT, $time);

        return $response
            ->withAddedHeader('Cache-Control', $cache_control)
            ->withAddedHeader('Last-Modified', $last_modified);
    }

    /**
     * Attach a nocache cache limiter header to the given response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @see https://github.com/php/php-src/blob/PHP-7.0/ext/session/session.c#L1304-L1314
     */
    private function attachNocacheCacheLimiterHeader(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withAddedHeader('Expires', self::EXPIRED)
            ->withAddedHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->withAddedHeader('Pragma', 'no-cache');
    }

    /**
     * Attach a session cookie to the given response.
     *
     * @param \Psr\Http\Message\ResponseInterface   $response
     * @param int                                   $time
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @see https://github.com/php/php-src/blob/PHP-7.0/ext/session/session.c#L1402-L1476
     */
    private function attachSessionCookie(ResponseInterface $response, int $time): ResponseInterface
    {
        // Get the session id, name and the cookie options.
        $id = session_id();
        $name = session_name();
        $options = session_get_cookie_params();

        // Create a cookie header.
        $header = urlencode($name) . '=' . urlencode($id);

        if ($options['lifetime'] > 0) {

            $expires = gmdate(self::DATE_FORMAT, $time + $options['lifetime']);

            $header .= "; expires={$expires}; max-age={$options['lifetime']}";

        }

        if ($options['path']) $header .= "; path={$options['path']}";
        if ($options['domain']) $header .= "; domain={$options['domain']}";
        if ($options['secure']) $header .= '; secure';
        if ($options['httponly']) $header .= '; httponly';

        // Return a new response with the cookie header.
        return $response->withAddedHeader('set-cookie', $header);
    }
}
