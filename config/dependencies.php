<?php

use Freemework\Container\Container;
use Laminas\Stratigility\MiddlewarePipe;
use Freemework\Http\Router\Router;
use Freemework\Http\Router\RouteCollection;
use Freemework\Http\Application;
use Laminas\Diactoros\{ServerRequestFactory, ResponseFactory, Response};
use Laminas\Stratigility\Handler\NotFoundHandler;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Freemework\Template\Twig\Extension\RouteExtension;
use Freemework\Template\Twig\TwigRenderer;

$container = new Container();

$container->set(MiddlewarePipe::class, new MiddlewarePipe());
$container->set(RouteCollection::class, new RouteCollection($container->get(MiddlewarePipe::class)));
$container->set(Router::class, new Router($container->get(RouteCollection::class)));
$container->set(
    Application::class,
    new Application(
        $container,
        $container->get(MiddlewarePipe::class),
        $container->get(RouteCollection::class)
    )
);

return $container;
