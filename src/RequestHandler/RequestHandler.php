<?php
/**
 * @author debuss-a
 */

namespace Borsch\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplStack;

/**
 * Class App
 * @package Borsch\RequestHandler
 */
class RequestHandler implements RequestHandlerInterface
{

    /** @var SplStack */
    protected $stack;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->stack = new SplStack();
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return $this
     */
    public function middleware(MiddlewareInterface $middleware): self
    {
        $this->stack->push($middleware);

        return $this;
    }

    /**
     * @param array $middlewares
     * @return $this
     */
    public function middlewares(array $middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->middleware($middleware);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->stack->shift()->process($request, $this);
    }
}
