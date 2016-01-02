<?php
namespace PTS\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MiddlewareManager
{
    /** @var callable[]|MiddlewareInterface[] */
    protected $middlewares = [];

    /**
     * @param callable|MiddlewareInterface $middleware
     * @return $this
     */
    public function push(callable $middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|null|mixed
     */
    public function __invoke(RequestInterface $request)
    {
        if (empty($this->middlewares)) {
           return null;
        }

        $middleware = array_shift($this->middlewares);
        return call_user_func_array($middleware, [$request, $this]);
    }
}
