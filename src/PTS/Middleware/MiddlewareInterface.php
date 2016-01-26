<?php
namespace PTS\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return ResponseInterface|mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next);

}
