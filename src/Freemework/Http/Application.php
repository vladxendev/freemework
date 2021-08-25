<?php

/**
 * FreeMeWork Framework (https://github.com/vladxendev/freemework)
 *
 */

declare(strict_types=1);

namespace Freemework\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Laminas\Diactoros\{ServerRequestFactory, ResponseFactory, Response, ServerRequest};
use Laminas\HttpHandlerRunner\Emitter\{EmitterStack, SapiEmitter};
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\MiddlewarePipe;
use Freemework\Container\Container;
use Freemework\Http\Router\Router;
use Freemework\Http\Router\RouteCollection;
use Freemework\Template\TemplateRenderer;
use Freemework\Template\Twig\Extension\RouteExtension;
use Freemework\Template\Twig\TwigRenderer;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Application
{
	public const ROOT_DIR = ROOT_DIR;
    public const SRC_DIR = self::ROOT_DIR . DIRECTORY_SEPARATOR . 'src';
    public const APP_DIR = self::ROOT_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'App';
    public const CONF_DIR = self::ROOT_DIR . DIRECTORY_SEPARATOR . 'config';
    public const TPL_DIR = self::ROOT_DIR . DIRECTORY_SEPARATOR . 'templates';
    
    /**
	 * @var ContainerInterface $container Container
	 */
    public ContainerInterface $container;

	/**
	 * @var MiddlewareInterface $middleware MiddlewarePipe
	 */
    public MiddlewareInterface $middleware;

	/**
	 * @var Router $router Router
	 */
    public Router $router;

	/**
	 * @var RouteCollection $routes Route[]
	 */
    public RouteCollection $routes;

	/**
	 * @var TemplateRenderer $template TwigRenderer
	 */
    public TemplateRenderer $template;

	/**
	 * @param ContainerInterface $container Container
     * @param MiddlewareInterface $middleware MiddlewarePipe
     * @param RouteCollection $routes Route[]
	 */
    public function __construct(ContainerInterface $container, MiddlewareInterface $middleware, RouteCollection $routes)
    {
        $this->container = $container;
        $this->middleware = $middleware;
        $this->routes = $routes;
        $this->router = new Router($this->routes);
        $loader = new FilesystemLoader();
        $loader->addPath(self::TPL_DIR);
        $twig = new Environment($loader, ['cache' => false, 'auto_reload' => true]);
        $this->template = new TwigRenderer($twig , [new RouteExtension($this->router)]);
    }

	/**
	 * @param ServerRequestInterface $request ServerRequest
	 */
    public function run(ServerRequestInterface $request)
    {
        $runner = new RequestHandlerRunner(
            $this->middleware,
            $this->container->get(SapiEmitter::class),
            function () use ($request) {
                return $request;
            },
            function (\Throwable $e) {
                $response = (new ResponseFactory())->createResponse(200);
                $response->getBody()->write(sprintf(
                    'An error occurred: %s',
                    $e->getMessage()
                ));
                return $response;
            }
        );

        $runner->run();
    }
}
