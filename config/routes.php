<?php

use Freemework\Container\Container;
use Laminas\Stratigility\MiddlewarePipe;
use Laminas\Diactoros\{ServerRequestFactory, ResponseFactory, Response};
use Freemework\Http\Router\Router;
use Freemework\Http\Router\RouteCollection;
use Freemework\Http\Router\RouteGroup;
use Freemework\Http\Application;
use Laminas\Stratigility\Handler\NotFoundHandler;
use App\Http\Action\HomeAction;

$app->routes->get('home', '/', [new HomeAction($app->template), 'home']);
