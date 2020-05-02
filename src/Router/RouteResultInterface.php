<?php
/**
 * @author debuss-a
 * @link https://docs.mezzio.dev/mezzio/v3/features/router/interface/
 */

namespace Borsch\Router;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Interface RouteResultInterface
 */
interface RouteResultInterface extends MiddlewareInterface
{

    /**
     * Create an instance representing a route success from the matching route.
     *
     * @param RouteInterface $route
     * @param array $params Parameters associated with the matched route, if any.
     * @return RouteResultInterface
     */
    public static function fromRoute(RouteInterface $route, array $params = []) : RouteResultInterface;

    /**
     * Create an instance representing a route failure.
     *
     * @param null|array $methods HTTP methods allowed for the current URI, if any.
     *     null is equivalent to allowing any HTTP method; empty array means none.
     * @return RouteResultInterface
     */
    public static function fromRouteFailure(?array $methods) : RouteResultInterface;

    /**
     * Does the result represent successful routing?
     */
    public function isSuccess() : bool;

    /**
     * Retrieve the route that resulted in the route match.
     *
     * @return false|null|Route false if representing a routing failure;
     *     null if not created via fromRoute(); Route instance otherwise.
     */
    public function getMatchedRoute();

    /**
     * Retrieve the matched route name, if possible.
     *
     * If this result represents a failure, return false; otherwise, return the
     * matched route name.
     *
     * @return false|string
     */
    public function getMatchedRouteName();

    /**
     * Returns the matched params.
     */
    public function getMatchedParams() : array;

    /**
     * Is this a routing failure result?
     */
    public function isFailure() : bool;

    /**
     * Does the result represent failure to route due to HTTP method?
     */
    public function isMethodFailure() : bool;

    /**
     * Retrieve the allowed methods for the route failure.
     *
     * @return string[] HTTP methods allowed
     */
    public function getAllowedMethods() : array;
}
