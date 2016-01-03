<?php

use Psr\Http\Message\ServerRequestInterface;

// not implements MiddlewareInterface, it is callable type handler
class MiddlewareC
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next - MiddlewareManager
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $response = $next($request);
        $response['paged'] = ['cursor' => 1000];
        return $response;
    }
}