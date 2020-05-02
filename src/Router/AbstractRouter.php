<?php
/**
 * @author debuss-a
 */

namespace Borsch\Router;

use InvalidArgumentException;
use League\Uri\Contracts\UriException;
use League\Uri\UriTemplate;

/**
 * Class AbstractRouter
 * @package Borsch\Router
 */
abstract class AbstractRouter implements RouterInterface
{

    /** @var RouteInterface[]  */
    protected $routes = [];

    /**
     * @inheritDoc
     */
    public function addRoute(RouteInterface $route): void
    {
        if (isset($this->routes[$route->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'A similar route name (%s) has already been provided.',
                $route->getName()
            ));
        }

        $this->routes[$route->getName()] = $route;
    }

    /**
     * @inheritDoc
     */
    public function generateUri(string $name, array $substitutions = []): string
    {
        if (!isset($this->routes[$name])) {
            return '';
        }

        $route = $this->routes[$name];
        $template = new UriTemplate($route->getPath());

        try {
            $uri = $template->expand($substitutions);
        } catch (UriException $exception) {
            $uri = '';
        }

        return (string)$uri;
    }
}
