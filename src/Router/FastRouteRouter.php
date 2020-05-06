<?php
/**
 * @author debuss-a
 */

namespace Borsch\Router;

use FastRoute\DataGenerator\GroupCountBased as RouteGenerator;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParser;
use InvalidArgumentException;
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

    /** @var RouteCollector */
    protected $router;

    /** @var string */
    protected $cache_file;

    /**
     * FastRouteRouter constructor.
     */
    public function __construct()
    {
        $this->router = new RouteCollector(new RouteParser(), new RouteGenerator());
    }

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
     * @return false|int
     */
    protected function cacheRoutes()
    {
        $dir = dirname($this->cache_file);

        if (!is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('The cache directory "%s" does not exist', $dir));
        }

        if (!is_writable($dir)) {
            throw new InvalidArgumentException(sprintf('The cache directory "%s" is not writable', $dir));
        }

        if (file_exists($this->cache_file) && ! is_writable($this->cache_file)) {
            throw new InvalidArgumentException(sprintf('The cache file %s is not writable', $this->cache_file));
        }

        return file_put_contents(
            $this->cache_file,
            sprintf('<?php return %s;', var_export((array)$this->router->getData(), true))
        );
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
     * @return array
     */
    protected function getDispatchData(): array
    {
        if ($this->cache_file && file_exists($this->cache_file)) {
            if (!is_readable($this->cache_file)) {
                throw new InvalidArgumentException(sprintf('The cache file %s is not readable', $this->cache_file));
            }

            set_error_handler(function () {}, E_WARNING);
            $data = require_once $this->cache_file;
            restore_error_handler();

            if (is_array($data)) {
                return $data;
            }

            throw new InvalidArgumentException(sprintf(
                'Invalid cache file "%s"; cache file MUST return an array',
                $this->cache_file
            ));
        }

        foreach ($this->routes as $route) {
            $this->router->addRoute($route->getAllowedMethods(), $route->getPath(), $route->getPath());
        }

        if ($this->cache_file) {
            $this->cacheRoutes();
        }

        return $this->router->getData();
    }

    /**
     * @inheritDoc
     */
    public function match(ServerRequestInterface $request): RouteResultInterface
    {
        $dispatcher = $dispatcher = $this->getDispatcher(function () {
            return new GroupCountBased($this->getDispatchData());
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
