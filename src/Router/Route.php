<?php
/**
 * @author debuss-a
 */

namespace Borsch\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class Route
 */
class Route implements RouteInterface
{

    /** @var string */
    protected $path;

    /** @var MiddlewareInterface */
    protected $middleware;

    /** @var array */
    protected $methods;

    /** @var array */
    protected $options;

    /** @var string */
    protected $name;

    /**
     * Route constructor.
     * @param string $path
     * @param MiddlewareInterface $middleware
     * @param array $methods
     * @param string|null $name
     */
    public function __construct(string $path, MiddlewareInterface $middleware, ?array $methods = null, ?string $name = null)
    {
        $this->path = $path;
        $this->middleware = $middleware;
        $this->methods = $methods ?: ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];
        $this->name = $name ?: sprintf(
            '%s^%s',
            $this->path,
            implode(':', $this->methods)
        );
    }

    /**
     * @param array $an_array
     * @return RouteInterface
     */
    public static function __set_state(array $an_array): RouteInterface
    {
        return new Route(
            $an_array['path'],
            $an_array['middleware'],
            $an_array['methods'],
            $an_array['name']
        );
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->middleware->process($request, $handler);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return MiddlewareInterface
     */
    public function getMiddleware(): MiddlewareInterface
    {
        return $this->middleware;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function allowsMethod(string $method): bool
    {
        return in_array($method, $this->methods);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getAllowedMethods(): ?array
    {
        return $this->methods;
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->options ?: [];
    }
}
