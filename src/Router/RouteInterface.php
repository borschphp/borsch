<?php
/**
 * @author debuss-a
 * @link https://docs.mezzio.dev/mezzio/v3/features/router/interface/
 */

namespace Borsch\Router;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Interface RouteInterface
 */
interface RouteInterface extends MiddlewareInterface
{

    /**
     * @return string
     */
    public function getPath() : string;

    /**
     * Set the route name.
     *
     * @param string $name
     */
    public function setName(string $name) : void;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return MiddlewareInterface
     */
    public function getMiddleware() : MiddlewareInterface;

    /**
     * @return array|null
     */
    public function getAllowedMethods() : ?array;

    /**
     * Indicate whether the specified method is allowed by the route.
     *
     * @param string $method
     * @return bool
     */
    public function allowsMethod(string $method) : bool;

    /**
     * @param array $options
     */
    public function setOptions(array $options) : void;

    /**
     * @return array
     */
    public function getOptions() : array;
}
