<?php
/**
 * @author debuss-a
 * @link https://docs.mezzio.dev/mezzio/v3/features/router/interface/
 */

namespace Borsch\Router;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * Interface defining required router capabilities.
 */
interface RouterInterface
{
    /**
     * Add a route.
     *
     * This method adds a route against which the underlying implementation may
     * match. Implementations MUST aggregate route instances, but MUST NOT use
     * the details to inject the underlying router until `match()` and/or
     * `generateUri()` is called.  This is required to allow consumers to
     * modify route instances before matching (e.g., to provide route options,
     * inject a name, etc.).
     *
     * The method MUST raise Exception\RuntimeException if called after either `match()`
     * or `generateUri()` have already been called, to ensure integrity of the
     * router between invocations of either of those methods.
     *
     * @param RouteInterface $route
     * @throws RuntimeException when called after match() or
     *     generateUri() have been called.
     */
    public function addRoute(RouteInterface $route) : void;

    /**
     * Match a request against the known routes.
     *
     * Implementations will aggregate required information from the provided
     * request instance, and pass them to the underlying router implementation;
     * when done, they will then marshal a `RouteResult` instance indicating
     * the results of the matching operation and return it to the caller.
     *
     * @param ServerRequestInterface $request
     * @return RouteResultInterface
     */
    public function match(ServerRequestInterface $request) : RouteResultInterface;

    /**
     * Generate a URI from the named route.
     *
     * Takes the named route and any substitutions, and attempts to generate a
     * URI from it. Additional router-dependent options may be passed.
     *
     * The URI generated MUST NOT be escaped. If you wish to escape any part of
     * the URI, this should be performed afterwards; consider passing the URI
     * to league/uri to encode it.
     *
     * @param string $name
     * @param array $substitutions
     * @return string
     * @see https://github.com/auraphp/Aura.Router/blob/3.x/docs/generating-paths.md
     * @see https://docs.laminas.dev/laminas-router/routing/
     */
    public function generateUri(string $name, array $substitutions = []) : string;
}
