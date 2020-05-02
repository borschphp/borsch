<?php
/**
 * @author debuss-a
 */

namespace Borsch\Application;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class CallableMiddleware
 * @package Borsch\Application
 */
class CallableMiddleware implements MiddlewareInterface
{

    /** @var callable */
    protected $middleware;

    /**
     * CallableMiddleware constructor.
     * @param callable $middleware
     */
    public function __construct(callable $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return ($this->middleware)($request, $handler);
    }
}
