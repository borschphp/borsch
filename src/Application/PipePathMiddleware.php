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
 * Class PipePathMiddleware
 * @package Borsch\Application
 */
class PipePathMiddleware implements MiddlewareInterface
{

    /** @var string */
    protected $path;

    /** @var MiddlewareInterface */
    protected $middleware;

    /**
     * PipePathMiddleware constructor.
     * @param string $path
     * @param MiddlewareInterface $middleware
     */
    public function __construct(string $path, MiddlewareInterface $middleware)
    {
        $this->path = $path;
        $this->middleware = $middleware;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (strpos($request->getUri()->getPath(), $this->path) === 0) {
            return $this->middleware->process($request, $handler);
        }

        return $handler->handle($request);
    }
}