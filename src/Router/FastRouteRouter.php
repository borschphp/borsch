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
                    $route
                );
            }
        });

        $route_info = $dispatcher->dispatch(
            $request->getMethod(),
            rawurldecode($request->getUri()->getPath())
        );

        if ($route_info[0] == Dispatcher::FOUND) {
            $route = $route_info[1];
            $vars = $route_info[2];

            return RouteResult::fromRoute($route, $vars);
        }

        return RouteResult::fromRouteFailure(
            $route_info[0] == Dispatcher::METHOD_NOT_ALLOWED ?
                $route_info[1] : []
        );
    }
}
