<?php

namespace PTS\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareManager
{
    /** @var callable[]|MiddlewareInterface[] */
    protected $middlewares = [];
    /** @var array|callable[] */
    protected $exceptionHandlers = [];

    public function push(callable $middleware, callable $exceptionHandler = null): self
    {
        $this->middlewares[] = $middleware;
        $this->exceptionHandlers[] = $exceptionHandler;
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed|null|ResponseInterface
     *
     * @throws \Throwable
     */
    public function __invoke(ServerRequestInterface $request)
    {
        if (empty($this->middlewares)) {
            return null;
        }

        $middleware = array_shift($this->middlewares);
        $exceptionHandler = array_shift($this->exceptionHandlers);

        try {
            $response = $middleware($request, $this);
        } catch (\Throwable $exception) {
            if ($exceptionHandler === null) {
                throw $exception;
            }

            $response = $exceptionHandler($exception);
        }

        return $response;
    }
}
