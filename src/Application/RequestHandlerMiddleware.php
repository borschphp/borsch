<?php
/**
 * @author debuss-a
 */

namespace Borsch\Application;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RequestHandlerMiddleware
 * @package Borsch\Application
 */
class RequestHandlerMiddleware implements RequestHandlerInterface, MiddlewareInterface
{

    /** @var RequestHandlerInterface */
    protected $handler;

    /**
     * RequestHandlerMiddleware constructor.
     * @param RequestHandlerInterface $handler
     */
    public function __construct(RequestHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->handler->handle($request);
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handler->handle($request);
    }
}
