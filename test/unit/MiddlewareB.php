<?php

use PTS\Middleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewareB implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next - MiddlewareManager
     * @return ResponseInterface|mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        // in request work...
        return $next($request);
    }
}