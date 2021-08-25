<?php
declare(strict_types=1);

namespace Freemework\Http\Router;

use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Freemework\Http\Router\RouteCollection;
use Freemework\Http\Router\Route\Route;
use Freemework\Http\Router\MatchedResult;
use Freemework\Http\Router\Exception\{RequestNotMatchedException, RouteNotFoundException};
use InvalidArgumentException;

use function in_array;
use function is_string;
use function preg_match;
use function preg_replace_callback;

class Router
{
	/**
	 * @var RouteCollection $routes Route[]
	 */
    private RouteCollection $routes;

	/**
	 * @param RouteCollection $routes Route[]
	 */
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function match(ServerRequestInterface $request): ?Route
    {
        foreach ($this->routes->getRoutes() as $route) {
            if ($result = $route->match($request)) {
                return $result;
                $this->routes->setCurrent($result);
            }
        }

        throw new RequestNotMatchedException($request);
    }

    public function generate($name, array $params = []): string
    {
        foreach ($this->routes->getRoutes() as $route) {
            if (null !== $url = $route->generate($name, array_filter($params))) {
                return $url;
            }
        }

        throw new RouteNotFoundException($name, $params);
    }

    public function dispatch(ServerRequestInterface $request): ?Route
    {
        $match = $this->match($request);

        if ($match instanceof Route) {
            foreach ($match->getAttributes() as $attribute => $value) {
                $request = $request->withAttribute($attribute, $value);
            }
        }
        
        return $match;
    }

    public function handle(ServerRequestInterface $request)
    {
        //return $this->dispatch($request);
    }
}
