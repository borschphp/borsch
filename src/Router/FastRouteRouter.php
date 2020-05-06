<?php
/**
 * @author debuss-a
 */

namespace Borsch\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use function FastRoute\cachedDispatcher;
use function FastRoute\simpleDispatcher;

/**
 * Class FastRouteRouter
 * @package Borsch\Router
 */
class FastRouteRouter extends AbstractRouter
{

    use HttpMethodTrait;

    /** @var string */
    protected $cache_file;

    /**
     * @return null|string
     */
    public function getCacheFile(): ?string
    {
        return $this->cache_file;
    }

    /**
     * @param string $cache_file
     */
    public function setCacheFile(string $cache_file): void
    {
        $this->cache_file = $cache_file;
    }

    /**
     * @param callable $callable
     * @return Dispatcher
     */
    protected function getDispatcher(callable $callable): Dispatcher
    {
        if (is_string($this->cache_file)) {
            return cachedDispatcher($callable, [
                'cacheFile' => $this->cache_file
            ]);
        }

        return simpleDispatcher($callable);
    }

    /**
     * @inheritDoc
     */
    public function match(ServerRequestInterface $request): RouteResultInterface
    {
        $dispatcher = $this->getDispatcher(function (RouteCollector $collector) {
            foreach ($this->routes as $route) {
                $collector->addRoute(
                    $route->getAllowedMethods(),
                    $route->getPath(),
                    $route->getPath()
                );
            }
        });

        $route_info = $dispatcher->dispatch(
            $request->getMethod(),
            rawurldecode($request->getUri()->getPath())
        );

        if ($route_info[0] == Dispatcher::FOUND) {
            return $this->getMatchedRoute($route_info, $request->getMethod());
        }

        return RouteResult::fromRouteFailure(
            $route_info[0] == Dispatcher::METHOD_NOT_ALLOWED ?
                $route_info[1] : []
        );
    }

    /**
     * @param array $route_info
     * @param string $method
     * @return RouteResultInterface
     */
    protected function getMatchedRoute(array $route_info, string $method): RouteResultInterface
    {
        $path  = $route_info[1];

        /** @var RouteInterface $route */
        $route = array_reduce($this->routes, function ($matched, $route) use ($path, $method) {
            if ($matched) {
                return $matched;
            }

            if ($path != $route->getPath()) {
                return $matched;
            }

            if (!$route->allowsMethod($method)) {
                return $matched;
            }

            return $route;
        }, false);

        if (false === $route) {
            // Shouldn't happen...
            return RouteResult::fromRouteFailure($route_info[1]);
        }

        return RouteResult::fromRoute($route, $route_info[2]);
    }
}
