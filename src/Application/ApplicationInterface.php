<?php
/**
 * @author debuss-a
 */

namespace Borsch\Application;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Interface defining a Borsch Application.
 */
interface ApplicationInterface
{

    /**
     * Add a GET Route to the application router instance.
     *
     * @param string $path
     * @param callable|MiddlewareInterface $middleware
     * @param string|null $name
     */
    public function get(string $path, $middleware, ?string $name = null): void;

    /**
     * Add a POST Route to the application router instance.
     *
     * @param string $path
     * @param callable|MiddlewareInterface $middleware
     * @param string|null $name
     */
    public function post(string $path, $middleware, ?string $name = null): void;

    /**
     * Add a PUT Route to the application router instance.
     *
     * @param string $path
     * @param callable|MiddlewareInterface $middleware
     * @param string|null $name
     */
    public function put(string $path, $middleware, ?string $name = null): void;

    /**
     * Add a DELETE Route to the application router instance.
     *
     * @param string $path
     * @param callable|MiddlewareInterface $middleware
     * @param string|null $name
     */
    public function delete(string $path, $middleware, ?string $name = null): void;

    /**
     * Add a PATH Route to the application router instance.
     *
     * @param string $path
     * @param callable|MiddlewareInterface $middleware
     * @param string|null $name
     */
    public function path(string $path, $middleware, ?string $name = null): void;

    /**
     * Add a HEAD Route to the application router instance.
     *
     * @param string $path
     * @param callable|MiddlewareInterface $middleware
     * @param string|null $name
     */
    public function head(string $path, $middleware, ?string $name = null): void;

    /**
     * Add an OPTIONS Route to the application router instance.
     *
     * @param string $path
     * @param callable|MiddlewareInterface $middleware
     * @param string|null $name
     */
    public function options(string $path, $middleware, ?string $name = null): void;

    /**
     * Add a Route to all methods to the application router instance.
     *
     * @param string $path
     * @param callable|MiddlewareInterface $middleware
     * @param string|null $name
     */
    public function any(string $path, $middleware, ?string $name = null): void;

    /**
     * Pipe a middleware to the pipeline.
     *
     * If two parameters are present, the first one must be a string representing a path to segregate
     * with the second one.
     *
     * @param string|callable|RequestHandlerInterface|MiddlewareInterface $middleware_or_path
     * @param string|callable|RequestHandlerInterface|MiddlewareInterface $middleware
     */
    public function pipe($middleware_or_path, $middleware = null): void;

    /**
     * Run the application.
     *
     * @param ServerRequestInterface $server_request
     */
    public function run(ServerRequestInterface $server_request): void;
}
