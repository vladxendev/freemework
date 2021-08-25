<?php
declare(strict_types=1);

namespace Freemework\Http\Router;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Psr\Http\Server\RequestHandlerInterface;
use Freemework\Http\Router\Route\Route;
use Freemework\Http\Router\Route\RegexpRoute;
use Freemework\Http\Router\RouteCollection;

use function sprintf;
use function ltrim;

class RouteGroup
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var RouteCollection
     */
    protected $collection;

    /**
     * @var string
     */
    protected $prefix;

    public function __construct(string $prefix, callable $callback, RouteCollection $collection)
    {
        $this->callback = $callback;
        $this->collection = $collection;
        $this->prefix = sprintf('/%s', ltrim($prefix, '/'));
    }

    public function __invoke(): void
    {
        ($this->callback)($this);
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function any($name, $pattern, $handler): Route
    {
        return $this->collection->addRoute(new RegexpRoute($name, sprintf('/%s', ltrim($this->prefix . $pattern, '/')), $handler, []));
    }

    public function get($name, $pattern, $handler): Route
    {
        return $this->collection->addRoute(new RegexpRoute($name, sprintf('/%s', ltrim($this->prefix . $pattern, '/')), $handler, [RequestMethod::METHOD_GET]));
    }

    public function post($name, $pattern, $handler): Route
    {
        return $this->collection->addRoute(new RegexpRoute($name, sprintf('/%s', ltrim($this->prefix . $pattern, '/')), $handler, [RequestMethod::METHOD_POST]));
    }

    public function put($name, $pattern, $handler): Route
    {
        return $this->collection->addRoute(new RegexpRoute($name, sprintf('/%s', ltrim($this->prefix . $pattern, '/')), $handler, [RequestMethod::METHOD_PUT]));
    }

    public function patch($name, $pattern, $handler): Route
    {
        return $this->collection->addRoute(new RegexpRoute($name, sprintf('/%s', ltrim($this->prefix . $pattern, '/')), $handler, [RequestMethod::METHOD_PATCH]));
    }

    public function delete($name, $pattern, $handler): Route
    {
        return $this->collection->addRoute(new RegexpRoute($name, sprintf('/%s', ltrim($this->prefix . $pattern, '/')), $handler, [RequestMethod::METHOD_DELETE]));
    }

    public function head($name, $pattern, $handler): Route
    {
        return $this->collection->addRoute(new RegexpRoute($name, sprintf('/%s', ltrim($this->prefix . $pattern, '/')), $handler, [RequestMethod::METHOD_HEAD]));
    }

    public function options($name, $pattern, $handler): Route
    {
        return $this->collection->addRoute(new RegexpRoute($name, sprintf('/%s', ltrim($this->prefix . $pattern, '/')), $handler, [RequestMethod::METHOD_OPTIONS]));
    }

    public function middleware(array $middleware)
    {
        $routes = $this->collection->getRoutes();
        foreach ($routes as $route) {
            $route->middleware($middleware);
        }
    }
}
