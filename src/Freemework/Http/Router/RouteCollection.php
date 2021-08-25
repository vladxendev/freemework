<?php
declare(strict_types=1);

namespace Freemework\Http\Router;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Freemework\Http\Router\Route\Route;
use Freemework\Http\Router\Route\RegexpRoute;
use Freemework\Http\Router\RouteGroup;

class RouteCollection
{
	/**
	 * @var array $routes Route[]
	 */
    private array $routes = [];

	/**
	 * @var array $groups RouteGroup[]
	 */
    private array $groups = [];

	/**
	 * @var array $current Route
	 */
    private Route $current;

	/**
	 * @var MiddlewareInterface $middleware MiddlewarePipe
	 */
    private MiddlewareInterface $middleware;

	/**
	 * @param MiddlewareInterface $middleware MiddlewarePipe
	 */
    public function __construct(MiddlewareInterface $middleware)
    {
        $this->middleware = $middleware;
    }

    public function addRoute(Route $route): Route
    {
        $this->routes[] = $this->current = $route;
        return $this->current;
    }
    
    public function any($name, $pattern, $handler): Route
    {
        return $this->addRoute(new RegexpRoute($name, $pattern, $handler, []));
    }

    public function get($name, $pattern, $handler): Route
    {
        return $this->addRoute(new RegexpRoute($name, $pattern, $handler, [RequestMethod::METHOD_GET]));
    }

    public function post($name, $pattern, $handler): Route
    {
        return $this->addRoute(new RegexpRoute($name, $pattern, $handler, [RequestMethod::METHOD_POST]));
    }

    public function put($name, $pattern, $handler): Route
    {
        return $this->addRoute(new RegexpRoute($name, $pattern, $handler, [RequestMethod::METHOD_PUT]));
    }

    public function patch($name, $pattern, $handler): Route
    {
        return $this->addRoute(new RegexpRoute($name, $pattern, $handler, [RequestMethod::METHOD_PATCH]));
    }

    public function delete($name, $pattern, $handler): Route
    {
        return $this->addRoute(new RegexpRoute($name, $pattern, $handler, [RequestMethod::METHOD_DELETE]));
    }

    public function head($name, $pattern, $handler): Route
    {
        return $this->addRoute(new RegexpRoute($name, $pattern, $handler, [RequestMethod::METHOD_HEAD]));
    }

    public function options($name, $pattern, $handler): Route
    {
        return $this->addRoute(new RegexpRoute($name, $pattern, $handler, [RequestMethod::METHOD_OPTIONS]));
    }

    /**
     * @return array Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function tokens(array $tokens)
    {
        $this->current->tokens($tokens);
    }

    public function defaults(array $defaults)
    {
        $this->current->defaults($defaults);
    }

    public function group(string $prefix, callable $group)
    {
        $group = new RouteGroup($prefix, $group, $this);
        $this->groups[] = $group;
        $group();
    }

    public function middleware(array $middleware)
    {
        $this->current->middleware($middleware);
    }

    /**
     * @return array RouteGroup[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    public function setCurrent($route)
    {
        $this->current = $route;
    }

    public function getCurrent(): Route
    {
        return $this->current;
    }
}
