<?php
/**
 * @author debuss-a
 */

namespace Borsch\Application;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class MiddlewareResolver
 * @package Borsch\Application
 */
class MiddlewareResolver
{

    /** @var ContainerInterface */
    protected $container;

    /**
     * MiddlewareResolver constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string|callable|RequestHandlerInterface|MiddlewareInterface $middleware
     * @return MiddlewareInterface
     */
    public function resolve($middleware): MiddlewareInterface
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        if ($middleware instanceof RequestHandlerInterface) {
            return new RequestHandlerMiddleware($middleware);
        }

        if (is_callable($middleware)) {
            return new CallableMiddleware($middleware);
        }

        if (!is_string($middleware) || $middleware == '') {
            throw new InvalidArgumentException('Provided middleware is not acceptable.');
        }

        return new LazyLoadingMiddleware($middleware, $this->container);
    }
}
