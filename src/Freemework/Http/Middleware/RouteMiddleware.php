<?php

namespace Freemework\Http\Middleware;

use Freemework\Http\Router\Exception\RequestNotMatchedException;
use Freemework\Http\Router\Route\Route;
use Freemework\Http\Router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Http\Middleware\NotFoundHandler;
use Freemework\Template\TemplateRenderer;

class RouteMiddleware implements MiddlewareInterface
{
    private Router $router;
    private MiddlewareInterface $middleware;

	/**
	 * @var TemplateRenderer $template TwigRenderer
	 */
    public TemplateRenderer $template;

    public function __construct(Router $router, MiddlewareInterface $middleware, TemplateRenderer $template)
    {
        $this->router = $router;
        $this->middleware = $middleware;
        $this->template = $template;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $handler = $this->router->match($request);
            if ($handler instanceof Route) {
                foreach ($handler->getAttributes() as $attribute => $value) {
                    $request = $request->withAttribute($attribute, $value);
                }
                
                $middlewares = $handler->getMiddlewares();
                if (!empty($middlewares)) {
                    foreach ($middlewares as $middleware) {
                        if ($middleware instanceof MiddlewareInterface) {
                            $this->middleware->pipe($middleware);
                        }
                    }
                }
            }

            return $handler->handle($request);
        } catch (RequestNotMatchedException $e) {
            $handler = new NotFoundHandler($this->template);
            return $handler->handle($request);
        }
    }
}