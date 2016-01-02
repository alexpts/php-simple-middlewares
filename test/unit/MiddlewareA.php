<?php

use PTS\Middleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewareA implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next - MiddlewareManager
     * @return ResponseInterface|mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return [
            'status' => 200,
            'body' => 'Hello World',
            'from' => 'A'
        ];
    }
}