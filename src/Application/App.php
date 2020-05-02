<?php
/**
 * @author debuss-a
 */

namespace Borsch\Application;

use Borsch\RequestHandler\Emitter;
use Borsch\Router\RouterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class App
 * @package Borsch\Application
 */
class App implements ApplicationInterface
{

    /** @var RequestHandlerInterface */
    protected $request_handler;

    /** @var RouterInterface */
    protected $router;

    /** @var ContainerInterface */
    protected $container;

    /** @var MiddlewareResolver */
    protected $resolver;

    /**
     * App constructor.
     * @param RequestHandlerInterface $request_handler
     * @param RouterInterface $router
     * @param ContainerInterface $container
     */
    public function __construct(RequestHandlerInterface $request_handler, RouterInterface $router, ContainerInterface $container)
    {
        $this->request_handler = $request_handler;
        $this->router = $router;
        $this->container = $container;

        $this->resolver = new MiddlewareResolver($this->container);
    }

    /**
     * @param string|callable|RequestHandlerInterface|MiddlewareInterface $middleware_or_path
     * @param null|string|callable|RequestHandlerInterface|MiddlewareInterface $middleware
     */
    public function pipe($middleware_or_path, $middleware = null): void
    {
        $middleware = $middleware ?: $middleware_or_path;
        $path = $middleware === $middleware_or_path ? '/' : $middleware_or_path;

        $middleware = $path != '/' ?
            new PipePathMiddleware($path, $this->resolver->resolve($middleware)) :
            $this->resolver->resolve($middleware);

        $this->request_handler->middleware($middleware);
    }

    /**
     * @param ServerRequestInterface $server_request
     */
    public function run(ServerRequestInterface $server_request): void
    {
        $response = $this->request_handler->handle($server_request);

        $emitter = new Emitter();
        $emitter->emit($response);
    }

    /**
     * @inheritDoc
     */
    public function get(string $path, $middleware, ?string $name = null): void
    {
        $this->router->{__FUNCTION__}($path, $this->resolver->resolve($middleware), $name);
    }

    /**
     * @inheritDoc
     */
    public function post(string $path, $middleware, ?string $name = null): void
    {
        $this->router->{__FUNCTION__}($path, $this->resolver->resolve($middleware), $name);
    }

    /**
     * @inheritDoc
     */
    public function put(string $path, $middleware, ?string $name = null): void
    {
        $this->router->{__FUNCTION__}($path, $this->resolver->resolve($middleware), $name);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, $middleware, ?string $name = null): void
    {
        $this->router->{__FUNCTION__}($path, $this->resolver->resolve($middleware), $name);
    }

    /**
     * @inheritDoc
     */
    public function path(string $path, $middleware, ?string $name = null): void
    {
        $this->router->{__FUNCTION__}($path, $this->resolver->resolve($middleware), $name);
    }

    /**
     * @inheritDoc
     */
    public function head(string $path, $middleware, ?string $name = null): void
    {
        $this->router->{__FUNCTION__}($path, $this->resolver->resolve($middleware), $name);
    }

    /**
     * @inheritDoc
     */
    public function options(string $path, $middleware, ?string $name = null): void
    {
        $this->router->{__FUNCTION__}($path, $this->resolver->resolve($middleware), $name);
    }

    /**
     * @inheritDoc
     */
    public function any(string $path, $middleware, ?string $name = null): void
    {
        $this->router->{__FUNCTION__}($path, $this->resolver->resolve($middleware), $name);
    }
}
