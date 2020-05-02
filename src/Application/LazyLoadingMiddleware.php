<?php
/**
 * @author debuss-a
 */

namespace Borsch\Application;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class LazyLoadingMiddleware
 * @package Borsch\Application
 */
class LazyLoadingMiddleware implements MiddlewareInterface
{

    /** @var string */
    protected $middleware;

    /** @var ContainerInterface */
    protected $container;

    /**
     * LazyLoadingMiddleware constructor.
     * @param string $middleware
     * @param ContainerInterface $container
     */
    public function __construct(string $middleware, ContainerInterface $container)
    {
        $this->middleware = $middleware;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var RequestHandlerInterface|MiddlewareInterface $middleware */
        $middleware = $this->container->get($this->middleware);

        if ($middleware instanceof RequestHandlerInterface) {
            return $middleware->handle($request);
        }

        return $middleware->process($request, $handler);
    }
}
